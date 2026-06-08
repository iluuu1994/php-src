# PoC Plan: 16-byte → 8-byte tagged `zval`

> Status: design document for a proof-of-concept. Not intended to be mergeable as-is.

## 1. Goal & scope

Shrink `zval` from 16 bytes
(`{ zend_value value(8); uint32 u1.type_info(4); uint32 u2(4) }`)
to a single tagged 64-bit word.

This is a **proof of concept**: we optimize for "basic PHP scripts run correctly
through the Zend core" over "every extension and the JIT still work." Doubles
become single-precision floats; JIT and address-of-`dval` extensions get disabled
rather than fixed. Concretely, the PoC builds with **`--disable-all`** (no bundled
extensions beyond the engine minimum) and **`--disable-opcache-jit`**, so the blast
radius is confined to the Zend core.

The work splits into four genuinely hard problems and a long tail of mechanical edits:

| Problem | Nature |
|---|---|
| A. The tagged encoding itself | Design — small, do first |
| B. Accessor macros as lvalues | Mechanical but large — compiler-driven |
| C. Relocating the 10 `u2` fields | Per-field design — the real engineering |
| D. Doubles → floats + integer overflow | Localized but semantically loud |

## 2. The encoding

`zval` stays a struct (so `zval*`, `sizeof`, and array strides keep working)
wrapping one word, plus an overlapping byte view for raw / UB-safe access:

```c
struct _zval_struct {
    union {
        uint64_t      word;     /* the tagged value */
        unsigned char raw[8];   /* byte view for raw copy / type punning */
    };
};
```

The `raw[]` member is belt-and-suspenders: accessing an object through a
`char`/`unsigned char` lvalue is always allowed (it never violates strict
aliasing), so it gives a well-defined path for any code that wants to `memcpy`
into/out of a zval or inspect its bytes. Note, though, that the encode/decode
arithmetic on `word` (shifts + ORs of integer types) is *already* UB-free — the
only genuine bit-cast is `float ↔ uint32` for `IS_DOUBLE`, handled separately
(see below), not by reinterpreting `word`.

Bit layout of `word`:

```
 bit:  63 ......................... 6 | 5 | 4 | 3 2 1 0
       └────────── payload ──────────┘ │C│ R│ └─type─┘
```

- **bits 0–3** — type tag (`IS_*`, raw value, **unchanged from upstream**)
- **bit 4** — `IS_TYPE_REFCOUNTED` flag (the old `type_flags` bit 0, relocated inline)
- **bit 5** — `IS_TYPE_COLLECTABLE` flag (the old `type_flags` bit 1, relocated inline)
- **bits 6+** — payload, for **every** tag (integer, double, *and* pointer)

The decisive simplification (a deliberate PoC choice): **every** value shifts its
payload by 6, integers included — not just pointers. That means bits 4 and 5 are
*never* payload for any value; they are flag bits for every tag. The refcount and
collectable tests are then a single bit test each:

```c
#define Z_TYPE_INFO_REFCOUNTED(w)   (((w) & 0x10) != 0)   /* bit 4 */
#define Z_TYPE_INFO_COLLECTABLE(w)  (((w) & 0x20) != 0)   /* bit 5 */
```

**This removes the need to reorder `IS_*` entirely.** Refcountedness is read from the
explicit flag (bit 4), not inferred from the tag value, so it does not matter that
the current upstream layout numbers the refcountable types 6–11 (`IS_STRING`=6 etc.).
Bit 4 is unambiguous because no integer (or any other) payload can ever set it — the
payload starts at bit 6.

Bit 4 must be a *real, stored* flag rather than something derived from the tag,
because refcountedness isn't a property of the type alone: a heap `IS_STRING` is
refcounted but an *interned* `IS_STRING` is not (upstream already encodes this —
`IS_INTERNED_STRING_EX == IS_STRING`, no refcount flag). Carrying the flag inline in
bit 4 preserves that distinction directly. `IS_TYPE_COLLECTABLE` (bit 5) is set only
together with bit 4, preserving the "collectable implies refcounted" invariant.

#### Why we *don't* reorder `IS_*` (and what it would buy)

The alternative — make refcountable tags ≥ 8 so bit 3 distinguishes them — was
explored and **rejected for the PoC**. It would let integers shift by only 4 (a
60-bit range) by using `(w & 0x18) == 0x18` to tell a refcounted pointer from an
integer whose payload bit 4 happens to be set. But reordering `IS_*` is far from
local: the `IS_*` values double as bit positions in two tightly-packed uint32 mask
layouts —

- the **type-inference mask** (`zend_type_info.h`): `MAY_BE_ARRAY_SHIFT == IS_REFERENCE`,
  so the type bits are stored twice (a base copy and an "array-of" copy shifted up by
  `IS_REFERENCE`) with array/RC flags packed above; used in the buildable set
  (`zend_execute.c`, the `gen_stub.php` generators, **and the Optimizer, which is
  compiled in**), and
- `zend_type.type_mask` (`zend_types.h`): reserves bits ≥ 18 for `_ZEND_TYPE_*` flags,
  with `IS_NEVER`=17 sitting on the exact ceiling.

Pushing the refcountable block to 8–13 forces `IS_REFERENCE`→12 and `IS_NEVER`→19,
which overflows *both* masks and would require relocating their flag regions (and a
cascade into `zend_compile.h`). That is the wall the earlier reorder attempt hit.
Shifting integers by 6 sidesteps all of it: the masks and the `IS_*` numbering stay
**byte-for-byte upstream**, the Optimizer keeps compiling unchanged, and we simply
spend two bits of integer range (58-bit instead of 60-bit) — a non-issue for a PoC,
where out-of-range integers overflow to float anyway. A production version that
wanted the extra two bits could revisit the reorder + mask repack later; it is
explicitly out of scope here.

### Payload encodings

| Kind | Encode | Decode | Range |
|---|---|---|---|
| Integer (`IS_LONG`) | `(lval << 6) \| IS_LONG` | `(int64_t)w >> 6` (arithmetic) | ±2⁵⁷; outside → float |
| Pointer (all ptr/refcounted) | `((uintptr_t)p << 6) \| collectbit << 5 \| refbit << 4 \| tag` | `(void*)((uintptr_t)w >> 6)` | needs `p < 2⁵⁸` (holds on x86-64/arm64 userspace) |
| Float (`IS_DOUBLE`) | `((uint64_t)bitcast<u32>((float)d) << 6) \| IS_DOUBLE` | `(double)bitcast<float>((u32)(w >> 6))` | single precision |

`bitcast<u32>(float)` / `bitcast<float>(u32)` above mean a **`memcpy`-based** bit
reinterpretation (or `__builtin_bit_cast` where available) — never a
`*(uint32_t*)&f` pointer cast, which would be strict-aliasing UB. Wrap it in a tiny
`static inline` helper so all `IS_DOUBLE` encode/decode goes through one audited
spot. This is the *only* place the PoC reinterprets bits; everything else is integer
arithmetic on `word`.
| `IS_NULL/FALSE/TRUE/UNDEF` | just the tag | — | — |

**Everything shifts by 6** — integers, doubles, and pointers alike — so bits 4 and 5
are uniformly the refcount/collectable flags for every tag. Shift-based decoding (not
alignment-bit stealing) means we don't need to touch `ZEND_MM_ALIGNMENT` (currently
8 = only 3 free low bits, far short of our 6).

Define a single `ZVAL_TAG_SHIFT` (= 6) macro so the layout is described in one place
and a future flag addition (or a production-mode reorder that reclaims integer bits)
is a one-line change.

### Type-tag space: exactly full

4 bits = 16 tags, and the live zval tags fit exactly in the **unchanged upstream
numbering**:

- standard types `IS_UNDEF`(0) … `IS_CONSTANT_AST`(11) take 0–11,
- the internal pointer/special tags `IS_INDIRECT`(12), `IS_PTR`(13),
  `IS_ALIAS_PTR`(14), `_IS_ERROR`(15) take 12–15.

Refcountedness is independent of the tag value — it's bit 4 of the word, set only for
the refcountable types when they actually hold a refcounted payload (e.g. not for an
interned `IS_STRING`). The non-refcounted tags simply never set bit 4.

`_IS_PLACEHOLDER`(20) is currently **completely unused** (only the `#define` exists,
no code reads or writes it), so it imposes no constraint. The fake type-hint types
(`IS_CALLABLE`, `IS_ITERABLE`, `IS_VOID`, `IS_MIXED`, `_IS_BOOL`, `_IS_NUMBER`, …) and
casts never appear as live zval tags — they only live in type masks — so they're
irrelevant to the 4-bit budget.

Note the budget is *exactly* full (all 16 slots used), so adding any new live zval
type later would require freeing a slot first.

## 3. The accessor-macro rewrite (problem B)

Today `Z_LVAL(z)`, `Z_STR(z)`, `Z_TYPE_INFO(z)` etc. expand to lvalues
(`(z).value.lval`) and are routinely **assigned to** and **address-taken**. A tagged
word can't be a settable lvalue. Strategy:

1. **Reads** (`Z_LVAL`, `Z_DVAL`, `Z_STR`, `Z_ARR`, `Z_OBJ`, `Z_RES`, `Z_REF`,
   `Z_PTR`, `Z_CE`, `Z_FUNC`, `Z_AST`, `Z_INDIRECT`, `Z_COUNTED`, `Z_TYPE`,
   `Z_TYPE_INFO`, `Z_TYPE_FLAGS`) become **rvalue decode expressions** (small
   `static inline` functions for type safety).
2. **Writes** go exclusively through the existing `ZVAL_*` setters (`ZVAL_LONG`,
   `ZVAL_STR`, `ZVAL_OBJ`, …), reimplemented to build the word. These already exist
   and are already the blessed write path almost everywhere.
3. **Driver = the compiler.** Once `Z_LVAL_P(z) = x` is no longer an lvalue, every
   offending site is a hard compile error. Fix each: `Z_LVAL_P(z)++` →
   `ZVAL_LONG(z, Z_LVAL_P(z)+1)`; address-of sites like `&Z_DVAL_P(z)` (e.g. in
   numeric-string parsing in `zend_operators.c`) → rewrite to point at a local
   `double`/`float` and store back via `ZVAL_DOUBLE`. This converts an unbounded
   search into a bounded "make it compile" loop.
4. `ZVAL_COPY_VALUE` collapses to a single word copy; `ZVAL_COPY` = word copy +
   conditional `GC_ADDREF`. The 32-bit `ww.w2` special-case and the
   "`u2`-doesn't-get-copied" subtlety both vanish.

Scope the rewrite to `Zend/` + `main/` + `TSRM/` + a minimal extension set
(`ext/standard` core, enough to run scripts). Everything else stays unbuilt.

## 4. Relocating `u2` (problem C — done first, incrementally)

This is the **first body of implementation work**, and it is done *before* touching
`value`/`u1`. Each `u2` field is moved to its new home one at a time on the unchanged
16-byte `zval`, building and testing green after each move. When the last consumer is
gone, the `u2` union is **deleted entirely** — a clean, fully-tested milestone. (Note:
removing `u2` leaves `zval` at `value(8) + u1(4)` = 12 bytes, which still rounds up to
**16** under 8-byte alignment, so `sizeof(zval)` doesn't shrink yet; the drop to 8
bytes happens later when `value`+`u1` are folded into the tagged word. The value of
this phase is that the subsequent fold becomes an isolated, `u2`-free transformation.)

Each of `u2`'s fields moves to a home determined by where its owning zval physically
lives:

| `u2` field | Owner zval lives in… | Relocation |
|---|---|---|
| **`next`** (hash chain) ✅ **done** | `Bucket` (only ever in hashed arrays, never in `arPacked`) | Added `uint32_t next;` to `Bucket`; rewrote the 43 `Z_NEXT(x->val)` → `x->next` in `zend_hash.c/.h`, `zend_string.c`, `zend_persist.c`. Build + array/Zend suites green under ASAN/UBSAN. (Bucket is temporarily **40 bytes** on the 16-byte zval; it returns to 32 — `8(val)+8(h)+8(key)+4(next)+4(pad)` — once the zval is 8 bytes.) |
| **`num_args`** (`EX(This)`) | `zend_execute_data` (a struct we own, not a generic container) | Add a field to `zend_execute_data`; redirect `ZEND_CALL_NUM_ARGS`. Hot but clean. |
| **`fe_pos` / `fe_iter_idx`** | FE_RESET result temp on the **VM stack** — holds array pointer *and* counter, can't coexist in 8 bytes | Allocate a tiny `{ zend_array*; uint32 pos; uint32 iter; }` iterator in `FE_RESET`, store its pointer (tagged `IS_PTR`) in the result, free in `FE_FREE`. Localized to FE_* opcodes; costs one alloc/foreach (acceptable for PoC). |
| **`guard`** | object's `properties_table[default_count]` reserved slot — stores property *name* + flags together | Move guard storage out of the zval: dedicated allocation / small struct keyed by name. Reworks `zend_get_property_guard` in `zend_object_handlers.c`. |
| **`opline_num`** (FAST_CALL) | FAST_CALL VM stack temp — holds the **exception object** (value union) *and* `opline_num` (`u2`) **at the same time**, so they can't share 8 bytes | Either a **"fat" temp** (allocate two sequential stack slots: one for the exception zval, one for `opline_num`), or **add an operand** for the second slot to all three ops that touch it (FAST_CALL, FAST_RET, DISCARD_EXCEPTION). The fat-temp option is preferable — it's confined to temp allocation in the compiler and leaves opcode operand counts unchanged. |
| **`lineno`** | `zend_ast_zval` (compile-time AST node) | Add `uint32_t lineno;` to `zend_ast_zval`. Not space-critical. Clean. |
| **`constant_flags`** | `zend_constant` entry | Add a field to `zend_constant`. Clean. |
| **`cache_slot`** | op_array literal (IS_CONSTANT_AST default value) — AST pointer + slot together | Store `cache_slot` alongside in a parallel literal-info slot or on the AST node. |
| **`extra`** | **many independent micro-uses** — drained one at a time, see checklist below | Each use gets its own relocation; the `u2.extra` member + `Z_EXTRA` macros are removed only once every use is gone. |

`num_args`/`lineno`/`constant_flags`/`next` all land in structs we control →
trivial. The genuinely fiddly four are **foreach state**, **property guard**,
**cache_slot**, and **FAST_CALL's opline_num**, because in each case the owning zval
packs a pointer (or other live value) + a uint32 that we can no longer co-locate.

Also relocate `Z_TYPE_EXTRA` (the 16-bit `u1.v.u.extra`) — same "no room" situation;
audit its (few) users and move them.

### `u2.extra` sub-uses (drain one at a time)

`Z_EXTRA` / `u2.extra` is overloaded across unrelated subsystems. Each is migrated as
its own step (build + test green), and only when all are gone are the `Z_EXTRA`/
`Z_EXTRA_P` macros and the `u2.extra` member removed.

- [x] **`ZEND_EXTRA_VALUE`** (`zend_compile.h`) — a numeric-string dim literal is
  compiled as two consecutive literals (the `LONG` index, then the original `STRING`
  at `op2.constant+1`, bug #63217); the `LONG` is flagged so dim opcodes do `dim++` to
  recover the string for `ArrayAccess`. Used by FETCH_DIM/ASSIGN_DIM/ASSIGN_DIM_OP
  families. Set in `zend_compile.c` (`zend_handle_numeric_dim`).
- [ ] **`ZEND_INIT_FCALL` offset** — the literal function name carries its offset into
  the global function table for faster lookup.
- [ ] **`ZEND_TYPE_ASSERT`** — same literal-offset trick (an `array_map()` optimization).
- [ ] **`zend_hash_sort_internal`** — stashes each bucket's original position in
  `Z_EXTRA(val)` for stable sorting.
- [ ] **`IS_PROP_UNINIT` / `IS_PROP_REINITABLE`** — per-property flags (explicitly
  `unset()`, and readonly-overwritable during `__clone`).
- [ ] **`INI_ZVAL_IS_NUMBER`** — flag set by `zend_ini_parser.y` to mark explicit numbers.
- [ ] **`VAR_WAKEUP_FLAG` / `VAR_UNSERIALIZE_FLAG`** — `ext/standard/var_unserializer.re`.
- [x] **`PHP_FUNCTION(array_map)`** — a local optimization.
- [x] **`ext/spl/spl_dllist.c`** — effectively revert commit `c51a5f02aeb160ef79e0acd6c65b8029ed8c0d3`.

Once the checklist is clear, delete `Z_EXTRA`/`Z_EXTRA_P` and the `u2.extra` member,
which empties the `u2` union entirely.

## 5. Doubles → floats + integer overflow (problem D)

- `ZVAL_DOUBLE` stores `(float)d`; `Z_DVAL` returns the float widened to `double`.
  Precision loss is accepted and expected; serialization/`var_dump` precision tests
  will diverge — fine for a PoC.
- `ZVAL_LONG` gains a range check: `|lval| < 2⁵⁷` stores as `IS_LONG`, else falls back
  to `ZVAL_DOUBLE((double)lval)`. **Overflow behavior: silently promote to float**
  (as specced — no debug assert). **Semantic hazard to document loudly:** code that
  does `ZVAL_LONG(z, big); assert(Z_TYPE_P(z)==IS_LONG)` will now sometimes see
  `IS_DOUBLE`. Most engine code already handles long↔double promotion in arithmetic,
  but direct-assumption sites exist.
- Core address-of-`dval` sites (numeric-string parsing in `zend_operators.c`) are
  rewritten to a local rather than pointing into the zval (see §3). Extensions with
  the same pattern (PDO drivers, etc.) aren't compiled under `--disable-all`, so
  they're simply out of scope.

## 6. Disable for the PoC

Build configuration: **`./configure --disable-all --disable-opcache-jit`**.

- **`--disable-opcache-jit`** drops the JIT, which hardcodes `sizeof(zval)==16`,
  shift-by-4 for packed strides, and dozens of `offsetof(zval, …)`. Out of scope.
- **`--disable-all`** drops every bundled extension, including those with raw
  zval-layout assumptions or `&dval` binding (PDO drivers, possibly `ext/ffi`).
- Keep `ZEND_STATIC_ASSERT(sizeof(zval)==8)` and assertions on the tag encoding to
  catch regressions.

Good news from recon: packed-array strides, `HT_PACKED_DATA_SIZE`, and the
`ZEND_HASH_FOREACH` macros are all expressed in `sizeof(zval)` and adapt
automatically; the SSE/AVX code in `zend_types.h` is for hash-index reset, not zval
copies; and the engine already tags pointers in `zend_weakrefs.c`, confirming the
approach is viable.

## 7. Implementation phases

The strategy is to **drain `u2` first**, one field at a time on the unchanged 16-byte
`zval`, keeping the build and tests green at every step, until `u2` can be deleted
entirely. Only then do we transform `value`+`u1` into the tagged word. This keeps the
risky encoding change isolated and every preceding step independently verifiable.

1. **Relocate `Bucket.next`** ✅ *done* — `u2.next` → `Bucket.next`, on the 16-byte
   zval; array/Zend suites green under ASAN/UBSAN.
2. **Drain the remaining `u2` fields** (§4), each as its own build-and-test-green step,
   easiest first:
   - struct-owned, trivial: `num_args` → `zend_execute_data`, `lineno` →
     `zend_ast_zval`, `constant_flags` → `zend_constant`;
   - then the fiddly pointer+uint32 cases: FAST_CALL `opline_num`, foreach
     `fe_pos`/`fe_iter_idx`, property `guard`, `cache_slot`; plus `extra` and
     `Z_TYPE_EXTRA`.
3. **Delete the `u2` union.** With no consumers left, remove the field and the
   `Z_*`/`Z_*_P` accessor macros for it. `zval` is now `value(8)+u1(4)` (still 16
   bytes under alignment), but `u2`-free. Build + full Zend/standard suites green —
   the key milestone before the encoding change.
4. **Fold `value`+`u1` into the 8-byte tagged word.** Define `ZVAL_TAG_SHIFT` (= 6),
   the bit-4 refcount / bit-5 collectable flags, encode/decode helpers (**no `IS_*`
   reorder**, see §2), and the `ZEND_STATIC_ASSERT`s (live tags fit 4 bits; bits 4/5
   don't overlap a tag). Reimplement all `Z_*` readers / `ZVAL_*` setters against the
   word, then fix every lvalue/address-of compile error across `Zend/`+`main/`
   (problem B). The longest phase.
5. **Doubles→floats + long overflow** (§5).
6. **Stabilize**: run `sapi/cli`, get `make test` (Zend core subset) green, triage.

Phases 1–3 each end at a buildable, testable 16-byte state; phase 4 is the flip.

## 8. Testing

- Phases 1–3 (the `u2` drain + deletion) are pure refactors: the **full** existing
  suite must stay green after each field move and after the union is removed.
- Phases 4–5 (the fold + floats): `Zend/tests/`, `tests/`, `ext/standard/tests/` core
  arithmetic/array/string. Expect and whitelist failures in float-precision and
  large-int tests.
- Smoke target: run a non-trivial script (array-heavy + OOP + closures + foreach)
  end-to-end under the CLI SAPI.
- Build with `--enable-debug` for the zval-encoding assertions; add a debug
  `ZVAL_VERIFY` that checks tag/payload invariants on decode.

## 9. Top risks

1. **Foreach iterator state** — the per-loop allocation is the most invasive
   control-flow change; if it's wrong, every `foreach` breaks. Prototype it on a
   branch early.
2. **Long→double silent promotion** breaking `IS_LONG` assumptions in hot paths.
   (Integer array keys and `nNextFreeElement` are *not* stored as zvals, so they're
   safe — but verify.)
3. **The lvalue sweep (Phase 3)** is large and easy to get subtly wrong where a macro
   was used in a clever in-place way; the compiler catches assignment but not aliasing.
4. **Refcount/collectable flag encoding** — refcountedness is now read from bit 4 of
   the word (not the tag), so every site that builds a zval must set bit 4/5 correctly;
   a missed flag silently corrupts GC. Mitigation: funnel all writes through the
   `ZVAL_*` setters and the `_EX` type+flag constants, and `ZEND_STATIC_ASSERT` that
   bits 4/5 never overlap a tag value. (Note: we deliberately do **not** reorder
   `IS_*` — uniform shift-by-6 avoids it; see §2 "Why we don't reorder `IS_*`".)

## Appendix: key references

- `zval` / `zend_value` / `Bucket`: `Zend/zend_types.h:321-390`
- Type constants: `Zend/zend_types.h:606-639`
- `u2` union: `Zend/zend_types.h:354-365`; accessor macros `:680-705`
- Refcount check: `Zend/zend_types.h:814-823`
- `ZVAL_COPY_VALUE` family: `Zend/zend_types.h:1397-1472`
- Hash chaining (`Z_NEXT`): `Zend/zend_hash.c` (~769, 797, 817, 892, 1282-1310)
- Foreach: `Zend/zend_vm_def.h` FE_RESET ~6916, FE_FETCH ~7214, FE_FREE ~3283
- Property guard: `Zend/zend_object_handlers.c:602-670`
- `ZEND_CALL_NUM_ARGS`: `Zend/zend_compile.h:712`
- JIT 16-byte assumption: `ext/opcache/jit/zend_jit_ir.c:16597`
- Existing pointer tagging precedent: `Zend/zend_weakrefs.c:52-54`
