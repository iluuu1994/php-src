/*
  +----------------------------------------------------------------------+
  | Copyright (c) The PHP Group                                          |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | https://www.php.net/license/3_01.txt                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
*/

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include "php.h"
#include "zend_interfaces.h"
#include "zend_exceptions.h"

#include "compact_vector_arginfo.h"
#include "compact_vector.h"
#include "ext/spl/spl_exceptions.h"

#include <stdint.h>

typedef enum {
	COMPACT_VECTOR_TYPE_INT8,
	COMPACT_VECTOR_TYPE_INT16,
	COMPACT_VECTOR_TYPE_INT32,
	COMPACT_VECTOR_TYPE_INT64,
	COMPACT_VECTOR_TYPE_FLOAT,
	COMPACT_VECTOR_TYPE_DOUBLE,
	COMPACT_VECTOR_TYPE_BOOL,
	COMPACT_VECTOR_TYPE_COUNTED,
} compact_vector_type;

typedef struct _compact_vector {
	compact_vector_type type;
	uint8_t element_size;
	zend_long size;
	zend_long capacity;
	char *elements;
} compact_vector;

typedef struct _compact_vector_object {
	compact_vector array;
	zend_object std;
} compact_vector_object;

static zend_object_handlers compact_vector_handlers;
PHPAPI zend_class_entry *php_ce_compact_vector;

static compact_vector_object *compact_vector_from_obj(zend_object *obj)
{
	return (compact_vector_object *)((char *)(obj) - XtOffsetOf(compact_vector_object, std));
}

static uint8_t compact_vector_element_size(compact_vector_type type)
{
	switch (type) {
		case COMPACT_VECTOR_TYPE_INT8:    return sizeof(int8_t);
		case COMPACT_VECTOR_TYPE_INT16:   return sizeof(int16_t);
		case COMPACT_VECTOR_TYPE_INT32:   return sizeof(int32_t);
		case COMPACT_VECTOR_TYPE_INT64:   return sizeof(int64_t);
		case COMPACT_VECTOR_TYPE_FLOAT:   return sizeof(float);
		case COMPACT_VECTOR_TYPE_DOUBLE:  return sizeof(double);
		case COMPACT_VECTOR_TYPE_BOOL:    return sizeof(uint8_t);
		case COMPACT_VECTOR_TYPE_COUNTED: return sizeof(void *);
	}
	ZEND_UNREACHABLE();
	return 0;
}

static bool compact_vector_parse_type(const zend_string *type_str, compact_vector_type *type)
{
	if (zend_string_equals_literal(type_str, "int8")) {
		*type = COMPACT_VECTOR_TYPE_INT8;
	} else if (zend_string_equals_literal(type_str, "int16")) {
		*type = COMPACT_VECTOR_TYPE_INT16;
	} else if (zend_string_equals_literal(type_str, "int32")) {
		*type = COMPACT_VECTOR_TYPE_INT32;
	} else if (zend_string_equals_literal(type_str, "int64")) {
		*type = COMPACT_VECTOR_TYPE_INT64;
	} else if (zend_string_equals_literal(type_str, "float")) {
		*type = COMPACT_VECTOR_TYPE_FLOAT;
	} else if (zend_string_equals_literal(type_str, "double")) {
		*type = COMPACT_VECTOR_TYPE_DOUBLE;
	} else if (zend_string_equals_literal(type_str, "bool")) {
		*type = COMPACT_VECTOR_TYPE_BOOL;
	} else if (zend_string_equals_literal(type_str, "counted")) {
		*type = COMPACT_VECTOR_TYPE_COUNTED;
	} else {
		return false;
	}
	return true;
}

static void *compact_vector_element_ptr(compact_vector *vec, zend_long index)
{
	return vec->elements + (size_t)index * vec->element_size;
}

static void compact_vector_release_counted_element(compact_vector *vec, zend_long index)
{
	ZEND_ASSERT(vec->type == COMPACT_VECTOR_TYPE_COUNTED);
	zend_refcounted *ref;
	memcpy(&ref, compact_vector_element_ptr(vec, index), sizeof(zend_refcounted *));
	if (ref != NULL) {
		if (!(GC_FLAGS(ref) & GC_IMMUTABLE)) {
			GC_DTOR(ref);
		}
	}
}

static void compact_vector_release_all_counted(compact_vector *vec)
{
	for (zend_long i = 0; i < vec->size; i++) {
		compact_vector_release_counted_element(vec, i);
	}
}

static void compact_vector_grow(compact_vector *vec, zend_long required_size)
{
	if (required_size <= vec->capacity) {
		return;
	}

	zend_long new_capacity = vec->capacity ? vec->capacity : 8;
	while (new_capacity < required_size) {
		new_capacity *= 2;
	}

	vec->elements = safe_erealloc(vec->elements, new_capacity, vec->element_size, 0);

	/* Zero-fill the new region */
	memset(
		vec->elements + (size_t)vec->capacity * vec->element_size,
		0,
		(size_t)(new_capacity - vec->capacity) * vec->element_size
	);

	vec->capacity = new_capacity;
}

static void compact_vector_ensure_size(compact_vector *vec, zend_long new_size)
{
	if (new_size > vec->capacity) {
		compact_vector_grow(vec, new_size);
	}
	if (new_size > vec->size) {
		vec->size = new_size;
	}
}

/* Offset conversion - same pattern as spl_fixedarray */
static zend_never_inline zend_ulong compact_vector_offset_convert_slow(const zval *offset)
{
try_again:
	switch (Z_TYPE_P(offset)) {
		case IS_STRING: {
			zend_ulong index;
			if (ZEND_HANDLE_NUMERIC(Z_STR_P(offset), index)) {
				return index;
			}
			break;
		}
		case IS_DOUBLE:
			return zend_dval_to_lval_safe(Z_DVAL_P(offset));
		case IS_LONG:
			return Z_LVAL_P(offset);
		case IS_FALSE:
			return 0;
		case IS_TRUE:
			return 1;
		case IS_REFERENCE:
			offset = Z_REFVAL_P(offset);
			goto try_again;
		case IS_RESOURCE:
			zend_use_resource_as_offset(offset);
			return Z_RES_HANDLE_P(offset);
	}

	zend_illegal_container_offset(php_ce_compact_vector->name, offset, BP_VAR_R);
	return 0;
}

static zend_always_inline zend_ulong compact_vector_offset_convert(const zval *offset)
{
	if (EXPECTED(Z_TYPE_P(offset) == IS_LONG)) {
		ZEND_ASSERT(!EG(exception));
		return Z_LVAL_P(offset);
	} else {
		return compact_vector_offset_convert_slow(offset);
	}
}

/* Read a stored element into a zval */
static void compact_vector_read_element(compact_vector *vec, zend_long index, zval *rv)
{
	void *ptr = compact_vector_element_ptr(vec, index);

	switch (vec->type) {
		case COMPACT_VECTOR_TYPE_INT8: {
			int8_t val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_LONG(rv, (zend_long)val);
			break;
		}
		case COMPACT_VECTOR_TYPE_INT16: {
			int16_t val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_LONG(rv, (zend_long)val);
			break;
		}
		case COMPACT_VECTOR_TYPE_INT32: {
			int32_t val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_LONG(rv, (zend_long)val);
			break;
		}
		case COMPACT_VECTOR_TYPE_INT64: {
			int64_t val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_LONG(rv, (zend_long)val);
			break;
		}
		case COMPACT_VECTOR_TYPE_FLOAT: {
			float val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_DOUBLE(rv, (double)val);
			break;
		}
		case COMPACT_VECTOR_TYPE_DOUBLE: {
			double val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_DOUBLE(rv, val);
			break;
		}
		case COMPACT_VECTOR_TYPE_BOOL: {
			uint8_t val;
			memcpy(&val, ptr, sizeof(val));
			ZVAL_BOOL(rv, val);
			break;
		}
		case COMPACT_VECTOR_TYPE_COUNTED: {
			zend_refcounted *ref;
			memcpy(&ref, ptr, sizeof(zend_refcounted *));
			if (ref == NULL) {
				ZVAL_NULL(rv);
			} else {
				uint8_t gc_type = GC_TYPE(ref);
				switch (gc_type) {
					case IS_STRING:
						ZVAL_STR_COPY(rv, (zend_string *)ref);
						break;
					case IS_ARRAY:
						ZVAL_ARR(rv, (zend_array *)ref);
						Z_ADDREF_P(rv);
						break;
					case IS_OBJECT:
						ZVAL_OBJ_COPY(rv, (zend_object *)ref);
						break;
					default:
						ZEND_UNREACHABLE();
				}
			}
			break;
		}
	}
}

/* Write a zval into a stored element. Assumes the value has been validated. */
static void compact_vector_write_element(compact_vector *vec, zend_long index, zval *value)
{
	void *ptr = compact_vector_element_ptr(vec, index);

	switch (vec->type) {
		case COMPACT_VECTOR_TYPE_INT8: {
			int8_t val = (int8_t)Z_LVAL_P(value);
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_INT16: {
			int16_t val = (int16_t)Z_LVAL_P(value);
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_INT32: {
			int32_t val = (int32_t)Z_LVAL_P(value);
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_INT64: {
			int64_t val = (int64_t)Z_LVAL_P(value);
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_FLOAT: {
			float val;
			if (Z_TYPE_P(value) == IS_LONG) {
				val = (float)Z_LVAL_P(value);
			} else {
				val = (float)Z_DVAL_P(value);
			}
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_DOUBLE: {
			double val;
			if (Z_TYPE_P(value) == IS_LONG) {
				val = (double)Z_LVAL_P(value);
			} else {
				val = Z_DVAL_P(value);
			}
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_BOOL: {
			uint8_t val = Z_TYPE_P(value) == IS_TRUE ? 1 : 0;
			memcpy(ptr, &val, sizeof(val));
			break;
		}
		case COMPACT_VECTOR_TYPE_COUNTED: {
			/* Release old value */
			compact_vector_release_counted_element(vec, index);
			zend_refcounted *ref;
			if (Z_TYPE_P(value) == IS_NULL) {
				ref = NULL;
			} else {
				ref = Z_COUNTED_P(value);
				GC_ADDREF(ref);
			}
			memcpy(ptr, &ref, sizeof(zend_refcounted *));
			break;
		}
	}
}

/* Validate that a zval matches the vector's type. Returns true on success. */
static bool compact_vector_validate_value(compact_vector *vec, zval *value)
{
	switch (vec->type) {
		case COMPACT_VECTOR_TYPE_INT8:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_LONG)) {
				zend_type_error("CompactVector<int8> value must be of type int, %s given", zend_zval_value_name(value));
				return false;
			}
			if (UNEXPECTED(Z_LVAL_P(value) < INT8_MIN || Z_LVAL_P(value) > INT8_MAX)) {
				zend_value_error("Value " ZEND_LONG_FMT " is out of range for int8 (-128 to 127)",
					Z_LVAL_P(value));
				return false;
			}
			return true;
		case COMPACT_VECTOR_TYPE_INT16:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_LONG)) {
				zend_type_error("CompactVector<int16> value must be of type int, %s given", zend_zval_value_name(value));
				return false;
			}
			if (UNEXPECTED(Z_LVAL_P(value) < INT16_MIN || Z_LVAL_P(value) > INT16_MAX)) {
				zend_value_error("Value " ZEND_LONG_FMT " is out of range for int16 (-32768 to 32767)",
					Z_LVAL_P(value));
				return false;
			}
			return true;
		case COMPACT_VECTOR_TYPE_INT32:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_LONG)) {
				zend_type_error("CompactVector<int32> value must be of type int, %s given", zend_zval_value_name(value));
				return false;
			}
			if (UNEXPECTED(Z_LVAL_P(value) < INT32_MIN || Z_LVAL_P(value) > INT32_MAX)) {
				zend_value_error("Value " ZEND_LONG_FMT " is out of range for int32 (-2147483648 to 2147483647)",
					Z_LVAL_P(value));
				return false;
			}
			return true;
		case COMPACT_VECTOR_TYPE_INT64:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_LONG)) {
				zend_type_error("CompactVector<int64> value must be of type int, %s given", zend_zval_value_name(value));
				return false;
			}
			/* int64 matches zend_long on 64-bit, no range check needed */
			return true;
		case COMPACT_VECTOR_TYPE_FLOAT:
		case COMPACT_VECTOR_TYPE_DOUBLE:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_LONG && Z_TYPE_P(value) != IS_DOUBLE)) {
				zend_type_error("CompactVector<%s> value must be of type int|float, %s given",
					vec->type == COMPACT_VECTOR_TYPE_FLOAT ? "float" : "double",
					zend_zval_value_name(value));
				return false;
			}
			return true;
		case COMPACT_VECTOR_TYPE_BOOL:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_TRUE && Z_TYPE_P(value) != IS_FALSE)) {
				zend_type_error("CompactVector<bool> value must be of type bool, %s given", zend_zval_value_name(value));
				return false;
			}
			return true;
		case COMPACT_VECTOR_TYPE_COUNTED:
			if (UNEXPECTED(Z_TYPE_P(value) != IS_STRING && Z_TYPE_P(value) != IS_ARRAY
					&& Z_TYPE_P(value) != IS_OBJECT && Z_TYPE_P(value) != IS_NULL)) {
				zend_type_error("CompactVector<counted> value must be of type string|array|object|null, %s given",
					zend_zval_value_name(value));
				return false;
			}
			return true;
	}
	ZEND_UNREACHABLE();
	return false;
}

/* Object handlers */

static zend_object *compact_vector_new(zend_class_entry *class_type)
{
	compact_vector_object *intern = zend_object_alloc(sizeof(compact_vector_object), class_type);

	zend_object_std_init(&intern->std, class_type);

	intern->array.type = COMPACT_VECTOR_TYPE_INT8;
	intern->array.element_size = 0;
	intern->array.size = 0;
	intern->array.capacity = 0;
	intern->array.elements = NULL;

	return &intern->std;
}

static void compact_vector_free(zend_object *object)
{
	compact_vector_object *intern = compact_vector_from_obj(object);

	if (intern->array.type == COMPACT_VECTOR_TYPE_COUNTED) {
		compact_vector_release_all_counted(&intern->array);
	}

	if (intern->array.elements) {
		efree(intern->array.elements);
	}

	zend_object_std_dtor(&intern->std);
}

static zend_object *compact_vector_clone(zend_object *old_object)
{
	compact_vector_object *old_intern = compact_vector_from_obj(old_object);
	zend_object *new_object = compact_vector_new(old_object->ce);
	compact_vector_object *new_intern = compact_vector_from_obj(new_object);

	new_intern->array.type = old_intern->array.type;
	new_intern->array.element_size = old_intern->array.element_size;
	new_intern->array.size = old_intern->array.size;
	new_intern->array.capacity = old_intern->array.capacity;

	if (old_intern->array.elements) {
		size_t buf_size = (size_t)old_intern->array.capacity * old_intern->array.element_size;
		new_intern->array.elements = emalloc(buf_size);
		memcpy(new_intern->array.elements, old_intern->array.elements, buf_size);

		if (old_intern->array.type == COMPACT_VECTOR_TYPE_COUNTED) {
			for (zend_long i = 0; i < new_intern->array.size; i++) {
				zend_refcounted *ref;
				memcpy(&ref, compact_vector_element_ptr(&new_intern->array, i), sizeof(zend_refcounted *));
				if (ref != NULL) {
					GC_ADDREF(ref);
				}
			}
		}
	}

	zend_objects_clone_members(new_object, old_object);

	return new_object;
}

static zval *compact_vector_read_dimension(zend_object *object, zval *offset, int type, zval *rv)
{
	compact_vector_object *intern = compact_vector_from_obj(object);

	if (UNEXPECTED(!offset)) {
		zend_throw_error(NULL, "[] operator not supported for reading from CompactVector");
		return NULL;
	}

	zend_ulong index = compact_vector_offset_convert(offset);
	if (UNEXPECTED(EG(exception))) {
		return NULL;
	}

	if (type == BP_VAR_IS && index >= (zend_ulong)intern->array.size) {
		return &EG(uninitialized_zval);
	}

	if (UNEXPECTED(index >= (zend_ulong)intern->array.size)) {
		zend_throw_exception(spl_ce_OutOfBoundsException, "Index out of range", 0);
		return NULL;
	}

	compact_vector_read_element(&intern->array, (zend_long)index, rv);
	return rv;
}

static void compact_vector_write_dimension(zend_object *object, zval *offset, zval *value)
{
	compact_vector_object *intern = compact_vector_from_obj(object);

	if (!compact_vector_validate_value(&intern->array, value)) {
		return;
	}

	zend_long index;
	if (!offset) {
		/* $v[] = value; append */
		index = intern->array.size;
	} else {
		zend_ulong uindex = compact_vector_offset_convert(offset);
		if (UNEXPECTED(EG(exception))) {
			return;
		}
		index = (zend_long)uindex;
		if (UNEXPECTED(index < 0)) {
			zend_throw_exception(spl_ce_OutOfBoundsException, "Index out of range", 0);
			return;
		}
	}

	compact_vector_ensure_size(&intern->array, index + 1);
	compact_vector_write_element(&intern->array, index, value);
}

static int compact_vector_has_dimension(zend_object *object, zval *offset, int check_empty)
{
	compact_vector_object *intern = compact_vector_from_obj(object);

	zend_ulong index = compact_vector_offset_convert(offset);
	if (UNEXPECTED(EG(exception))) {
		return 0;
	}

	if (index >= (zend_ulong)intern->array.size) {
		return 0;
	}

	if (intern->array.type == COMPACT_VECTOR_TYPE_COUNTED) {
		zend_refcounted *ref;
		memcpy(&ref, compact_vector_element_ptr(&intern->array, (zend_long)index), sizeof(zend_refcounted *));
		if (ref == NULL) {
			return 0;
		}
	}

	if (check_empty) {
		zval rv;
		compact_vector_read_element(&intern->array, (zend_long)index, &rv);
		bool result = zend_is_true(&rv);
		zval_ptr_dtor(&rv);
		return result;
	}

	return 1;
}

static void compact_vector_unset_dimension(zend_object *object, zval *offset)
{
	compact_vector_object *intern = compact_vector_from_obj(object);

	zend_ulong index = compact_vector_offset_convert(offset);
	if (UNEXPECTED(EG(exception))) {
		return;
	}

	if (UNEXPECTED(index >= (zend_ulong)intern->array.size)) {
		zend_throw_exception(spl_ce_OutOfBoundsException, "Index out of range", 0);
		return;
	}

	if (intern->array.type == COMPACT_VECTOR_TYPE_COUNTED) {
		compact_vector_release_counted_element(&intern->array, (zend_long)index);
	}

	memset(compact_vector_element_ptr(&intern->array, (zend_long)index), 0, intern->array.element_size);
}

static HashTable *compact_vector_get_gc(zend_object *obj, zval **table, int *n)
{
	compact_vector_object *intern = compact_vector_from_obj(obj);

	if (intern->array.type != COMPACT_VECTOR_TYPE_COUNTED || intern->array.size == 0) {
		*table = NULL;
		*n = 0;
		return NULL;
	}

	zend_get_gc_buffer *gc_buffer = zend_get_gc_buffer_create();
	for (zend_long i = 0; i < intern->array.size; i++) {
		zend_refcounted *ref;
		memcpy(&ref, compact_vector_element_ptr(&intern->array, i), sizeof(zend_refcounted *));
		if (ref != NULL) {
			zval tmp;
			uint8_t gc_type = GC_TYPE(ref);
			switch (gc_type) {
				case IS_STRING:
					ZVAL_STR(&tmp, (zend_string *)ref);
					break;
				case IS_ARRAY:
					ZVAL_ARR(&tmp, (zend_array *)ref);
					break;
				case IS_OBJECT:
					ZVAL_OBJ(&tmp, (zend_object *)ref);
					break;
				default:
					ZEND_UNREACHABLE();
					continue;
			}
			zend_get_gc_buffer_add_zval(gc_buffer, &tmp);
		}
	}

	zend_get_gc_buffer_use(gc_buffer, table, n);
	return NULL;
}

/* PHP methods */

ZEND_METHOD(CompactVector, __construct)
{
	zend_string *type_str;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_STR(type_str)
	ZEND_PARSE_PARAMETERS_END();

	compact_vector_object *intern = compact_vector_from_obj(Z_OBJ_P(ZEND_THIS));
	compact_vector_type type;

	if (!compact_vector_parse_type(type_str, &type)) {
		zend_argument_value_error(1, "must be one of \"int8\", \"int16\", \"int32\", \"int64\", "
			"\"float\", \"double\", \"bool\", or \"counted\", \"%s\" given", ZSTR_VAL(type_str));
		RETURN_THROWS();
	}

	intern->array.type = type;
	intern->array.element_size = compact_vector_element_size(type);
}

ZEND_METHOD(CompactVector, offsetExists)
{
	zval *offset;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_ZVAL(offset)
	ZEND_PARSE_PARAMETERS_END();

	RETURN_BOOL(compact_vector_has_dimension(Z_OBJ_P(ZEND_THIS), offset, 0));
}

ZEND_METHOD(CompactVector, offsetGet)
{
	zval *offset;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_ZVAL(offset)
	ZEND_PARSE_PARAMETERS_END();

	zval *result = compact_vector_read_dimension(Z_OBJ_P(ZEND_THIS), offset, BP_VAR_R, return_value);
	if (result && result != return_value) {
		RETURN_COPY(result);
	}
}

ZEND_METHOD(CompactVector, offsetSet)
{
	zval *offset, *value;

	ZEND_PARSE_PARAMETERS_START(2, 2)
		Z_PARAM_ZVAL(offset)
		Z_PARAM_ZVAL(value)
	ZEND_PARSE_PARAMETERS_END();

	compact_vector_write_dimension(Z_OBJ_P(ZEND_THIS), Z_ISNULL_P(offset) ? NULL : offset, value);
}

ZEND_METHOD(CompactVector, offsetUnset)
{
	zval *offset;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_ZVAL(offset)
	ZEND_PARSE_PARAMETERS_END();

	compact_vector_unset_dimension(Z_OBJ_P(ZEND_THIS), offset);
}

PHP_MINIT_FUNCTION(compact_vector)
{
	php_ce_compact_vector = register_class_CompactVector(zend_ce_arrayaccess);
	php_ce_compact_vector->create_object = compact_vector_new;
	php_ce_compact_vector->default_object_handlers = &compact_vector_handlers;

	memcpy(&compact_vector_handlers, &std_object_handlers, sizeof(zend_object_handlers));
	compact_vector_handlers.offset = XtOffsetOf(compact_vector_object, std);
	compact_vector_handlers.free_obj = compact_vector_free;
	compact_vector_handlers.clone_obj = compact_vector_clone;
	compact_vector_handlers.read_dimension = compact_vector_read_dimension;
	compact_vector_handlers.write_dimension = compact_vector_write_dimension;
	compact_vector_handlers.has_dimension = compact_vector_has_dimension;
	compact_vector_handlers.unset_dimension = compact_vector_unset_dimension;
	compact_vector_handlers.get_gc = compact_vector_get_gc;
	compact_vector_handlers.compare = zend_objects_not_comparable;

	return SUCCESS;
}
