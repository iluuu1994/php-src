--TEST--
NS global function assumption: deoptimization when shadow is declared at runtime
--FILE--
<?php

// Include a file with Ns\test() that calls strlen() unqualified.
// It should be compiled assuming strlen() resolves to \strlen().
include __DIR__ . '/ns_global_func_assumption_002.inc';

// First call: no shadow exists, uses global strlen() (constant-folded).
var_dump(Ns\test());

// Now declare Ns\strlen() which shadows the global strlen().
include __DIR__ . '/ns_global_func_assumption_002_shadow.inc';

// Second call: Ns\test() should be deoptimized and use Ns\strlen().
var_dump(Ns\test());

?>
--EXPECT--
int(5)
string(8) "shadow:5"
