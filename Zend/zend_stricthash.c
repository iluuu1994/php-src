/*
	Copyright (c) 2021 Tyson Andre

	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	- Redistributions of source code must retain the above copyright notice, this
	list of conditions and the following disclaimer.

	- Redistributions in binary form must reproduce the above copyright notice,
	this list of conditions and the following disclaimer in the documentation
	and/or other materials provided with the distribution.

	- Neither the name of the 'teds' nor the names of its contributors may
	be used to endorse or promote products derived from this software without
	specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
	ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
	LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
	CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
	THE POSSIBILITY OF SUCH DAMAGE.
*/

#include "php.h"
#include "php_ini.h"

// bswap compiler checks copied from https://github.com/google/cityhash/blob/8af9b8c2b889d80c22d6bc26ba0df1afb79a30db/src/city.cc#L50
//
// Copyright (c) 2011 Google, Inc.
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
#ifdef _MSC_VER
# include <stdlib.h>
# define ZEND_BSWAP_32(x) _byteswap_ulong(x)
# define ZEND_BSWAP_64(x) _byteswap_uint64(x)
#elif defined(__APPLE__)
// Mac OS X / Darwin features
# include <libkern/OSByteOrder.h>
# define ZEND_BSWAP_32(x) OSSwapInt32(x)
# define ZEND_BSWAP_64(x) OSSwapInt64(x)
#elif defined(__sun) || defined(sun)
# include <sys/byteorder.h>
# define ZEND_BSWAP_32(x) BSWAP_32(x)
# define ZEND_BSWAP_64(x) BSWAP_64(x)
#elif defined(__FreeBSD__)
# include <sys/endian.h>
# define ZEND_BSWAP_32(x) bswap32(x)
# define ZEND_BSWAP_64(x) bswap64(x)
#elif defined(__OpenBSD__)
# include <sys/types.h>
# define ZEND_BSWAP_32(x) swap32(x)
# define ZEND_BSWAP_64(x) swap64(x)
#elif defined(__NetBSD__)
# include <sys/types.h>
# include <machine/bswap.h>
# define ZEND_BSWAP_32(x) bswap32(x)
# define ZEND_BSWAP_64(x) bswap64(x)
#else
# include <byteswap.h>
# define ZEND_BSWAP_32(x) bswap_32(x)
# define ZEND_BSWAP_64(x) bswap_64(x)
#endif

#define ZEND_STRICTHASH_HASH_NULL 8310
#define ZEND_STRICTHASH_HASH_FALSE 8311
#define ZEND_STRICTHASH_HASH_TRUE 8312
#define ZEND_STRICTHASH_HASH_EMPTY_ARRAY 8313
/*
 * See https://en.wikipedia.org/wiki/NaN
 * For nan, the 12 most significant bits are:
 * - 1 sign bit (0 or 1)
 * - 11 sign bits
 * (and at least one of the significand bits must be non-zero)
 *
 * Here, 0xff is the most significant byte with the sign and part of the exponent,
 * and 0xf8 is the second most significant byte with part of the exponent and significand.
 *
 * Return an arbitrary choice of 0xff f_, with bytes in the reverse order.
 */
#define ZEND_STRICTHASH_HASH_NAN 0xf8ff

#define ZEND_STRICTHASH_HASH_OFFSET_DOUBLE 8315
#define ZEND_STRICTHASH_HASH_OFFSET_OBJECT 31415926
#define ZEND_STRICTHASH_HASH_OFFSET_RESOURCE 27182818

typedef struct _rec_prot_node {
	const void *elem;
	const struct _rec_prot_node *prev;
} rec_prot_node;

static zend_long zend_stricthash_array(HashTable *const ht, const rec_prot_node *const node);
static zend_long zend_stricthash_data_object(zend_object *const obj, const rec_prot_node *const node);
static uint64_t zend_convert_double_to_uint64_t(double value);
static zend_always_inline zend_long zend_stricthash_inner(zval *value, rec_prot_node *node);

static zend_long zend_stricthash_combine(zend_long lhs, zend_long rhs)
{
#if SIZEOF_ZEND_LONG >= 8
	lhs ^= rhs + 0x517cc1b727220a95 + (lhs << 6) + (lhs >> 2);
#else
	lhs ^= rhs + 0x9e3779b9 + (lhs << 6) + (lhs >> 2);
#endif
	return lhs;
}

static zend_always_inline uint64_t zend_inline_hash_of_uint64(uint64_t orig) {
	/* Copied from code written for igbinary. Works best when data that frequently
	 * differs is in the least significant bits of data. */
	uint64_t data = orig * 0x5e2d58d8b3bce8d9;
	return ZEND_BSWAP_64(data);
}

zend_long zend_stricthash_hash(zval *value) {
	uint64_t raw_data = zend_stricthash_inner(value, NULL);
	return zend_inline_hash_of_uint64(raw_data);
}

static zend_always_inline zend_long zend_stricthash_inner(zval *value, rec_prot_node *node) {
again:
	switch (Z_TYPE_P(value)) {
		case IS_NULL:
			return ZEND_STRICTHASH_HASH_NULL;
		case IS_FALSE:
			return ZEND_STRICTHASH_HASH_FALSE;
		case IS_TRUE:
			return ZEND_STRICTHASH_HASH_TRUE;
		case IS_LONG:
			return Z_LVAL_P(value);
		case IS_DOUBLE:
			return zend_convert_double_to_uint64_t(Z_DVAL_P(value)) + ZEND_STRICTHASH_HASH_OFFSET_DOUBLE;
		case IS_STRING:
			return ZSTR_HASH(Z_STR_P(value));
		case IS_ARRAY:
			return zend_stricthash_array(Z_ARR_P(value), node);
		case IS_OBJECT:
			if (Z_OBJCE_P(value)->ce_flags & ZEND_ACC_DATA_CLASS) {
				return zend_stricthash_data_object(Z_OBJ_P(value), node);
			} else {
				return Z_OBJ_HANDLE_P(value) + ZEND_STRICTHASH_HASH_OFFSET_OBJECT;
			}
		case IS_RESOURCE:
			return Z_RES_HANDLE_P(value) + ZEND_STRICTHASH_HASH_OFFSET_RESOURCE;
		case IS_REFERENCE:
			value = Z_REFVAL_P(value);
			goto again;
		case IS_INDIRECT:
			value = Z_INDIRECT_P(value);
			goto again;
		EMPTY_SWITCH_DEFAULT_CASE();
	}
}

inline static uint64_t zend_convert_double_to_uint64_t(double value) {
	if (value == 0) {
		/* Signed positive and negative 0 have different bits. However, $signedZero === $signedNegativeZero in php and many other languages. */
		return 0;
	}
	if (UNEXPECTED(isnan(value))) {
		return ZEND_STRICTHASH_HASH_NAN;
	}
	uint8_t *data = (uint8_t *)&value;
#ifndef WORDS_BIGENDIAN
	return
		(((uint64_t)data[0]) << 56) |
		(((uint64_t)data[1]) << 48) |
		(((uint64_t)data[2]) << 40) |
		(((uint64_t)data[3]) << 32) |
		(((uint64_t)data[4]) << 24) |
		(((uint64_t)data[5]) << 16) |
		(((uint64_t)data[6]) << 8) |
		(((uint64_t)data[7]));
#else
	return
		(((uint64_t)data[7]) << 56) |
		(((uint64_t)data[6]) << 48) |
		(((uint64_t)data[5]) << 40) |
		(((uint64_t)data[4]) << 32) |
		(((uint64_t)data[3]) << 24) |
		(((uint64_t)data[2]) << 16) |
		(((uint64_t)data[1]) << 8) |
		(((uint64_t)data[0]));
#endif
}

static bool zend_stricthash_protect_recursion(zend_refcounted *elem, const rec_prot_node *const node, rec_prot_node *const new_node)
{
	new_node->prev = node;
	new_node->elem = elem;

	if (UNEXPECTED(GC_IS_RECURSIVE(elem))) {
		for (const rec_prot_node *tmp = node; tmp != NULL; tmp = tmp->prev) {
			if (tmp->elem == elem) {
				zend_error_noreturn(E_ERROR, "Nesting level too deep - recursive dependency?");
			}
		}
		return false;
	}

	GC_PROTECT_RECURSION(elem);
	return true;
}

static zend_long zend_stricthash_array(HashTable *const ht, const rec_prot_node *const node) {
	if (zend_hash_num_elements(ht) == 0) {
		return ZEND_STRICTHASH_HASH_EMPTY_ARRAY;
	}

	uint64_t result = 1;
	bool protected_recursion = false;

	rec_prot_node new_node;
	rec_prot_node *new_node_ptr;

	if (!(GC_FLAGS(ht) & GC_IMMUTABLE)) {
		new_node_ptr = &new_node;
		protected_recursion = zend_stricthash_protect_recursion((zend_refcounted *)ht, node, &new_node);
	}

	zend_long num_key;
	zend_string *str_key;
	zval *field_value;
	ZEND_HASH_FOREACH_KEY_VAL(ht, num_key, str_key, field_value) {
		/* str_key is in a hash table, meaning that the hash was already computed. */
		result += str_key ? ZSTR_H(str_key) : (zend_ulong) num_key;
		zend_long field_hash = zend_stricthash_inner(field_value, new_node_ptr);
		result += (field_hash + (result << 7));
		result = zend_inline_hash_of_uint64(result);
	} ZEND_HASH_FOREACH_END();

	if (protected_recursion) {
		GC_UNPROTECT_RECURSION(ht);
	}

	return result;
}

static zend_long zend_stricthash_data_object(zend_object *const obj, const rec_prot_node *const node) {
	zend_long hash = ZSTR_HASH(obj->ce->name);

	rec_prot_node new_node;
	bool protected_recursion = zend_stricthash_protect_recursion((zend_refcounted *)obj, node, &new_node);

	if (obj->properties) {
		zend_string *key;
		zval *data;
		/* Data classes disallow ArrayObject, so there's no need to check for integer property keys. */
		ZEND_HASH_FOREACH_STR_KEY_VAL_IND(obj->properties, key, data) {
			hash = zend_stricthash_combine(hash, ZSTR_HASH(key));
			hash = zend_stricthash_combine(hash, zend_stricthash_hash(data));
		} ZEND_HASH_FOREACH_END();
	} else {
		for (uint32_t i = 0; i < obj->ce->default_properties_count; i++) {
			zend_property_info *prop_info = obj->ce->properties_info_table[i];
			hash = zend_stricthash_combine(hash, ZSTR_HASH(prop_info->name));
			hash = zend_stricthash_combine(hash, zend_stricthash_hash(OBJ_PROP_NUM(obj, i)));
		}
	}

	if (protected_recursion) {
		GC_UNPROTECT_RECURSION(obj);
	}

	return hash;
}
