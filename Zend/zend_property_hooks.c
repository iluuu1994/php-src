/*
   +----------------------------------------------------------------------+
   | Zend Engine                                                          |
   +----------------------------------------------------------------------+
   | Copyright (c) Zend Technologies Ltd. (http://www.zend.com)           |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
   | Authors: Ilija Tovilo <ilutov@php.net>                               |
   +----------------------------------------------------------------------+
*/

#include "zend.h"
#include "zend_property_hooks.h"

typedef struct {
	zend_object_iterator it;
	bool by_ref;
	zval properties;
	zval current_data;
} zend_hooked_object_iterator;

static int zend_hooked_object_it_valid(zend_object_iterator *iter);

static zend_array *zend_hooked_object_build_properties(zend_object *zobj)
{
	zend_class_entry *ce = zobj->ce;
	zend_array *properties = zend_new_array(ce->default_properties_count);
	zend_hash_real_init_mixed(properties);

	zend_property_info *prop_info;
	// FIXME: Count virtual properties on class
	int virtual_property_count = 0;
	ZEND_HASH_MAP_FOREACH_PTR(&ce->properties_info, prop_info) {
		if (prop_info->flags & ZEND_ACC_STATIC) {
			continue;
		}
		if (prop_info->flags & ZEND_ACC_VIRTUAL) {
			virtual_property_count++;
		}
		if (zend_check_property_access(zobj, prop_info->name, /* is_dynamic */ false) != SUCCESS) {
			continue;
		}
		if (prop_info->hooks) {
			_zend_hash_append_ptr(properties, prop_info->name, prop_info);
		} else {
			if (UNEXPECTED(Z_TYPE_P(OBJ_PROP(zobj, prop_info->offset)) == IS_UNDEF)) {
				HT_FLAGS(properties) |= HASH_FLAG_HAS_EMPTY_IND;
			}
			_zend_hash_append_ind(properties, prop_info->name,
				OBJ_PROP(zobj, prop_info->offset));
		}
	} ZEND_HASH_FOREACH_END();

	if (zobj->properties) {
		int stored_property_count = zend_array_count(&zobj->ce->properties_info) - virtual_property_count;
		zend_string *prop_name;
		zval *prop_value;
		ZEND_HASH_FOREACH_STR_KEY_VAL_FROM(zobj->properties, prop_name, prop_value, stored_property_count) {
			if (zend_check_property_access(zobj, prop_name, /* is_dynamic */ true) != SUCCESS) {
				continue;
			}
			Z_TRY_ADDREF_P(_zend_hash_append(properties, prop_name, prop_value));
		} ZEND_HASH_FOREACH_END();
	}

	return properties;
}

static void zend_hooked_object_it_fetch_current_data(zend_object_iterator *iter)
{
	if (zend_hooked_object_it_valid(iter) != SUCCESS) {
		return;
	}

	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zval_ptr_dtor(&hooked_iter->current_data);
	if (EG(exception)) {
		return;
	}
	ZVAL_UNDEF(&hooked_iter->current_data);
	zend_object *zobj = Z_OBJ_P(&iter->data);
	zend_array *properties = Z_ARR(hooked_iter->properties);
	zval *property = zend_hash_get_current_data(properties);
	if (Z_TYPE_P(property) == IS_PTR) {
		zend_property_info *prop_info = Z_PTR_P(property);
		zend_function *get = prop_info->hooks[ZEND_PROPERTY_HOOK_GET];
		if (get == NULL) {
			if (prop_info->flags & ZEND_ACC_VIRTUAL) {
				zend_throw_error(NULL, "Must not read from virtual property %s::$%s",
					ZSTR_VAL(zobj->ce->name), zend_get_unmangled_property_name(prop_info->name));
				return;
			}
			goto get_direct;
		}
		if (hooked_iter->by_ref && !(get->op_array.fn_flags & ZEND_ACC_RETURN_REFERENCE)) {
			zend_throw_error(NULL, "Must not fetch get hook of %s::$%s by-ref",
				ZSTR_VAL(zobj->ce->name), zend_get_unmangled_property_name(prop_info->name));
			return;
		}
		GC_ADDREF(zobj);
		// FIXME: We need the guard...
		// *guard |= IN_HOOK;
		zend_call_known_instance_method_with_0_params(get, zobj, &hooked_iter->current_data);
		// *guard &= ~IN_HOOK;
		OBJ_RELEASE(zobj);
	} else {
get_direct:
		ZVAL_DEINDIRECT(property);
		if (hooked_iter->by_ref
		 && Z_TYPE_P(property) != IS_REFERENCE
		 && Z_TYPE_P(property) != IS_UNDEF) {
			ZVAL_MAKE_REF(property);
		}
		ZVAL_COPY(&hooked_iter->current_data, property);
	}
	if (!hooked_iter->by_ref) {
		// FIXME: Is there a macro just for references?
		SEPARATE_ZVAL(&hooked_iter->current_data);
	}
}

static void zend_hooked_object_it_dtor(zend_object_iterator *iter)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zval_ptr_dtor(&iter->data);
	zval_ptr_dtor(&hooked_iter->properties);
	zval_ptr_dtor(&hooked_iter->current_data);
}

static int zend_hooked_object_it_valid(zend_object_iterator *iter)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zend_array *properties = Z_ARR(hooked_iter->properties);
	return zend_hash_has_more_elements(properties);
}

static zval *zend_hooked_object_it_get_current_data(zend_object_iterator *iter)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	return &hooked_iter->current_data;
}

static void zend_hooked_object_it_get_current_key(zend_object_iterator *iter, zval *key)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zend_array *properties = Z_ARR(hooked_iter->properties);
	zend_hash_get_current_key_zval(properties, key);
}

static void zend_hooked_object_it_move_forward(zend_object_iterator *iter)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zend_array *properties = Z_ARR(hooked_iter->properties);
	do {
		zend_hash_move_forward(properties);
		zend_hooked_object_it_fetch_current_data(iter);
	} while (!EG(exception)
	 && zend_hooked_object_it_valid(iter) == SUCCESS
	 && Z_TYPE(hooked_iter->current_data) == IS_UNDEF);
}

static void zend_hooked_object_it_rewind(zend_object_iterator *iter)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zend_array *properties = Z_ARR(hooked_iter->properties);
	zend_hash_internal_pointer_reset(properties);
	zend_hooked_object_it_fetch_current_data(iter);
}

static HashTable *zend_hooked_object_it_get_gc(zend_object_iterator *iter, zval **table, int *n)
{
	zend_hooked_object_iterator *hooked_iter = (zend_hooked_object_iterator*)iter;
	zend_get_gc_buffer *gc_buffer = zend_get_gc_buffer_create();
	zend_get_gc_buffer_add_zval(gc_buffer, &iter->data);
	zend_get_gc_buffer_add_zval(gc_buffer, &hooked_iter->properties);
	zend_get_gc_buffer_add_zval(gc_buffer, &hooked_iter->current_data);
	zend_get_gc_buffer_use(gc_buffer, table, n);
	return NULL;
}

static const zend_object_iterator_funcs zend_hooked_object_it_funcs = {
	zend_hooked_object_it_dtor,
	zend_hooked_object_it_valid,
	zend_hooked_object_it_get_current_data,
	zend_hooked_object_it_get_current_key,
	zend_hooked_object_it_move_forward,
	zend_hooked_object_it_rewind,
	NULL,
	zend_hooked_object_it_get_gc,
};

ZEND_API zend_object_iterator *zend_hooked_object_get_iterator(zend_class_entry *ce, zval *object, int by_ref)
{
	zend_hooked_object_iterator *iterator = emalloc(sizeof(zend_hooked_object_iterator));
	zend_iterator_init(&iterator->it);

	ZVAL_OBJ_COPY(&iterator->it.data, Z_OBJ_P(object));
	iterator->it.funcs = &zend_hooked_object_it_funcs;
	iterator->by_ref = by_ref;
	zend_array *properties = zend_hooked_object_build_properties(Z_OBJ_P(object));
	ZVAL_ARR(&iterator->properties, properties);
	ZVAL_UNDEF(&iterator->current_data);

	return &iterator->it;
}
