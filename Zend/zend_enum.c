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
#include "zend_API.h"
#include "zend_compile.h"
#include "zend_interfaces_arginfo.h"

#define ZEND_ENUM_PROPERTY_ERROR() \
	zend_throw_error(NULL, "Enum properties are immutable")

#define ZEND_ENUM_DISALLOW_MAGIC_METHOD(propertyName, methodName) \
	do { \
		if (ce->propertyName) { \
			zend_error_noreturn(E_COMPILE_ERROR, "Enum may not include %s", methodName); \
		} \
	} while (0);

static zend_object_handlers enum_handlers;

zend_object *zend_enum_new(zval *result, zend_class_entry *ce, zend_string *case_name, zval *scalar_zv)
{
	// Temporarily remove the ZEND_ACC_ENUM flag to allow instantiation
	ce->ce_flags &= ~ZEND_ACC_ENUM;
	object_init_ex(result, ce);
	ce->ce_flags |= ZEND_ACC_ENUM;

	zend_object *zobj = Z_OBJ_P(result);
	ZVAL_STR_COPY(OBJ_PROP_NUM(zobj, 0), case_name);
	if (scalar_zv != NULL) {
		ZVAL_COPY(OBJ_PROP_NUM(zobj, 1), scalar_zv);
	}

	zobj->handlers = &enum_handlers;

	return zobj;
}

static void zend_verify_enum_properties(zend_class_entry *ce)
{
	zend_property_info *property_info;

	ZEND_HASH_FOREACH_PTR(&ce->properties_info, property_info) {
		if (zend_string_equals_literal(property_info->name, "name")) {
			continue;
		}
		if (
			ce->enum_scalar_type != IS_UNDEF
			&& zend_string_equals_literal(property_info->name, "value")
		) {
			continue;
		}
		// FIXME: File/line number for traits?
		zend_error_noreturn(E_COMPILE_ERROR, "Enum \"%s\" may not include properties",
			ZSTR_VAL(ce->name));
	} ZEND_HASH_FOREACH_END();
}

static void zend_verify_enum_magic_methods(zend_class_entry *ce)
{
	// Only __get, __call and __invoke are allowed

	ZEND_ENUM_DISALLOW_MAGIC_METHOD(constructor, "__construct");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(destructor, "__destruct");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(clone, "__clone");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__get, "__get");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__set, "__set");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__unset, "__unset");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__isset, "__isset");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__tostring, "__toString");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__debugInfo, "__debugInfo");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__serialize, "__serialize");
	ZEND_ENUM_DISALLOW_MAGIC_METHOD(__unserialize, "__unserialize");

	const char* forbidden_methods[] = {
		"__sleep",
		"__wakeup",
		"__set_state",
	};

	uint32_t forbidden_methods_length = sizeof(forbidden_methods) / sizeof(forbidden_methods[0]);
	for (uint32_t i = 0; i < forbidden_methods_length; ++i) {
		const char *forbidden_method = forbidden_methods[i];

		if (zend_hash_str_find_ptr(&ce->function_table, forbidden_method, strlen(forbidden_method))) {
			zend_error_noreturn(E_COMPILE_ERROR, "Enum may not include magic method %s", forbidden_method);
		}
	}
}

void zend_verify_enum(zend_class_entry *ce)
{
	zend_verify_enum_properties(ce);
	zend_verify_enum_magic_methods(ce);
}

static zval *zend_enum_read_property(zend_object *zobj, zend_string *name, int type, void **cache_slot, zval *rv) /* {{{ */
{
	if (
		type == BP_VAR_W
		|| type == BP_VAR_RW
		|| type == BP_VAR_UNSET
	) {
		zend_throw_error(NULL, "Cannot acquire reference to property %s::$%s", ZSTR_VAL(zobj->ce->name), ZSTR_VAL(name));
		return &EG(uninitialized_zval);
	}

	return zend_std_read_property(zobj, name, type, cache_slot, rv);
}

ZEND_COLD zval *zend_enum_write_property(zend_object *object, zend_string *member, zval *value, void **cache_slot)
{
	ZEND_ENUM_PROPERTY_ERROR();
	return &EG(uninitialized_zval);
}

static ZEND_COLD void zend_enum_unset_property(zend_object *object, zend_string *member, void **cache_slot)
{
	ZEND_ENUM_PROPERTY_ERROR();
}

ZEND_API zval *zend_enum_get_property_ptr_ptr(zend_object *zobj, zend_string *name, int type, void **cache_slot)
{
	return NULL;
}

int zend_enum_compare_objects(zval *o1, zval *o2)
{
	if (Z_TYPE_P(o1) != IS_OBJECT || Z_TYPE_P(o2) != IS_OBJECT) {
		return ZEND_UNCOMPARABLE;
	}

	return Z_OBJ_P(o1) == Z_OBJ_P(o2) ? 0 : 1;
}

void zend_register_enum_ce(void)
{
	memcpy(&enum_handlers, &std_object_handlers, sizeof(zend_object_handlers));
	enum_handlers.read_property = zend_enum_read_property;
	enum_handlers.write_property = zend_enum_write_property;
	enum_handlers.unset_property = zend_enum_unset_property;
	enum_handlers.get_property_ptr_ptr = zend_enum_get_property_ptr_ptr;
	enum_handlers.clone_obj = NULL;
	enum_handlers.compare = zend_enum_compare_objects;
}

void zend_enum_add_interfaces(zend_class_entry *ce)
{
	uint32_t num_interfaces_before = ce->num_interfaces;

	ce->num_interfaces++;
	if (ce->enum_scalar_type != IS_UNDEF) {
		ce->num_interfaces++;
	}

	ce->interface_names = erealloc(ce->interface_names, sizeof(zend_class_name) * ce->num_interfaces);

	ce->interface_names[num_interfaces_before].name = zend_string_init("UnitEnum", sizeof("UnitEnum") - 1, 0);
	ce->interface_names[num_interfaces_before].lc_name = zend_string_init("unitenum", sizeof("unitenum") - 1, 0);

	if (ce->enum_scalar_type != IS_UNDEF) {
		ce->interface_names[num_interfaces_before + 1].name = zend_string_init("ScalarEnum", sizeof("ScalarEnum") - 1, 0);
		ce->interface_names[num_interfaces_before + 1].lc_name = zend_string_init("scalarenum", sizeof("scalarenum") - 1, 0);	
	}
}

static ZEND_NAMED_FUNCTION(zend_enum_cases_func)
{
	zend_class_entry *ce = execute_data->func->common.scope;
	zend_class_constant *c;

	if (zend_parse_parameters_none() == FAILURE) {
		RETURN_THROWS();
	}

	array_init(return_value);

	ZEND_HASH_FOREACH_PTR(&ce->constants_table, c) {
		if (!(c->value.u2.access_flags & ZEND_CLASS_CONST_IS_CASE)) {
			continue;
		}
		zval *zv = &c->value;
		if (Z_TYPE_P(zv) == IS_CONSTANT_AST) {
			zval_update_constant_ex(zv, c->ce);
			if (UNEXPECTED(EG(exception) != NULL)) {
				return;
			}
		}
		Z_ADDREF_P(zv);
		zend_hash_next_index_insert(Z_ARRVAL_P(return_value), zv);
	} ZEND_HASH_FOREACH_END();
}

static void zend_enum_from_base(INTERNAL_FUNCTION_PARAMETERS, bool try)
{
	zend_class_entry *ce = execute_data->func->common.scope;
	zend_string *string_key;
	zend_long long_key;

	zval *case_name_zv;
	if (ce->enum_scalar_type == IS_LONG) {
		if (zend_parse_parameters(ZEND_NUM_ARGS(), "l", &long_key) == FAILURE) {
			RETURN_THROWS();
		}

		case_name_zv = zend_hash_index_find(ce->enum_scalar_table, long_key);
	} else {
		if (zend_parse_parameters(ZEND_NUM_ARGS(), "S", &string_key) == FAILURE) {
			RETURN_THROWS();
		}

		ZEND_ASSERT(ce->enum_scalar_type == IS_STRING);
		case_name_zv = zend_hash_find(ce->enum_scalar_table, string_key);
	}

	if (case_name_zv == NULL) {
		if (try) {
			RETURN_NULL();
		} else {
			if (ce->enum_scalar_type == IS_LONG) {
				zend_value_error("%d is not a valid scalar value for enum \"%s\"", (int) long_key, ZSTR_VAL(ce->name));
			} else {
				ZEND_ASSERT(ce->enum_scalar_type == IS_STRING);
				zend_value_error("\"%s\" is not a valid scalar value for enum \"%s\"", ZSTR_VAL(string_key), ZSTR_VAL(ce->name));
			}
			RETURN_THROWS();
		}
	}

	ZEND_ASSERT(Z_TYPE_P(case_name_zv) == IS_STRING);
	zval *case_const_zv = zend_hash_find(&ce->constants_table, Z_STR_P(case_name_zv));
	ZEND_ASSERT(case_const_zv != NULL);
	zend_class_constant *c = Z_PTR_P(case_const_zv);
	zval *case_zv = &c->value;
	if (Z_TYPE_P(case_zv) == IS_CONSTANT_AST) {
		zval_update_constant_ex(case_zv, c->ce);
		if (UNEXPECTED(EG(exception) != NULL)) {
			RETURN_THROWS();
		}
	}

	ZVAL_COPY(return_value, case_zv);
}

static ZEND_NAMED_FUNCTION(zend_enum_from_func)
{
	zend_enum_from_base(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
}

static ZEND_NAMED_FUNCTION(zend_enum_try_from_func)
{
	zend_enum_from_base(INTERNAL_FUNCTION_PARAM_PASSTHRU, 1);
}

void zend_enum_register_funcs(zend_class_entry *ce)
{
	zend_string *cases_func_name = zend_string_init("cases", strlen("cases"), 1);
	zend_internal_function *cases_function = malloc(sizeof(zend_internal_function));
	memset(cases_function, 0, sizeof(zend_internal_function));
	cases_function->type = ZEND_INTERNAL_FUNCTION;
	cases_function->module = EG(current_module);
	cases_function->handler = zend_enum_cases_func;
	cases_function->function_name = cases_func_name;
	cases_function->scope = ce;
	cases_function->fn_flags = ZEND_ACC_PUBLIC|ZEND_ACC_STATIC|ZEND_ACC_HAS_RETURN_TYPE;
	cases_function->arg_info = (zend_internal_arg_info *) (arginfo_class_UnitEnum_cases + 1);
	zend_hash_add_ptr(&ce->function_table, cases_func_name, cases_function);

	if (ce->enum_scalar_type != IS_UNDEF) {
		zend_string *from_func_name = zend_string_init("from", strlen("from"), 1);
		zend_internal_function *from_function = malloc(sizeof(zend_internal_function));
		memset(from_function, 0, sizeof(zend_internal_function));
		from_function->type = ZEND_INTERNAL_FUNCTION;
		from_function->module = EG(current_module);
		from_function->handler = zend_enum_from_func;
		from_function->function_name = from_func_name;
		from_function->scope = ce;
		from_function->fn_flags = ZEND_ACC_PUBLIC|ZEND_ACC_STATIC|ZEND_ACC_HAS_RETURN_TYPE;
		from_function->num_args = 1;
		from_function->required_num_args = 1;
		from_function->arg_info = (zend_internal_arg_info *) (arginfo_class_ScalarEnum_from + 1);
		zend_hash_add_ptr(&ce->function_table, from_func_name, from_function);

		zend_internal_function *try_from_function = malloc(sizeof(zend_internal_function));
		memset(try_from_function, 0, sizeof(zend_internal_function));
		try_from_function->type = ZEND_INTERNAL_FUNCTION;
		try_from_function->module = EG(current_module);
		try_from_function->handler = zend_enum_try_from_func;
		try_from_function->function_name = zend_string_init("tryFrom", strlen("tryFrom"), 1);
		try_from_function->scope = ce;
		try_from_function->fn_flags = ZEND_ACC_PUBLIC|ZEND_ACC_STATIC|ZEND_ACC_HAS_RETURN_TYPE;
		try_from_function->num_args = 1;
		try_from_function->required_num_args = 1;
		try_from_function->arg_info = (zend_internal_arg_info *) (arginfo_class_ScalarEnum_tryFrom + 1);
		zend_hash_add_ptr(&ce->function_table, zend_string_init("tryfrom", strlen("tryfrom"), 1), try_from_function);
	}
}

void zend_enum_register_props(zend_class_entry *ce)
{
	zval name_default_value;
	ZVAL_UNDEF(&name_default_value);
	zend_type name_type = ZEND_TYPE_INIT_CODE(IS_STRING, 0, 0);
	zend_declare_typed_property(ce, ZSTR_KNOWN(ZEND_STR_NAME), &name_default_value, ZEND_ACC_PUBLIC, NULL, name_type);

	if (ce->enum_scalar_type != IS_UNDEF) {
		zval value_default_value;
		ZVAL_UNDEF(&value_default_value);
		zend_type value_type = ZEND_TYPE_INIT_CODE(ce->enum_scalar_type, 0, 0);
		zend_declare_typed_property(ce, ZSTR_KNOWN(ZEND_STR_VALUE), &value_default_value, ZEND_ACC_PUBLIC, NULL, value_type);
	}
}

zval *zend_enum_fetch_case_name(zend_object *zobj)
{
	ZEND_ASSERT(zobj->ce->ce_flags & ZEND_ACC_ENUM);
	return OBJ_PROP_NUM(zobj, 0);
}

zval *zend_enum_fetch_case_value(zend_object *zobj)
{
	ZEND_ASSERT(zobj->ce->ce_flags & ZEND_ACC_ENUM);
	ZEND_ASSERT(zobj->ce->enum_scalar_type != IS_UNDEF);
	return OBJ_PROP_NUM(zobj, 1);
}
