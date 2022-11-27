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
*/

#include "zend_pattern_matching.h"
#include "zend_compile.h"
#include "zend_execute.h"
#include "zend_exceptions.h"
#include "zend_type_info.h"

bool zend_pattern_match_ex(zval *zv, zend_ast *pattern);

static bool match_type(zval *zv, zend_ast *type_ast)
{
	zend_ast *class_name_ast = type_ast->child[0];
	if (class_name_ast) {
		if (Z_TYPE_P(zv) != IS_OBJECT) {
			return false;
		}

		zend_object *obj = Z_OBJ_P(zv);
		zend_string *class_name = zend_ast_get_str(class_name_ast);
		if (!zend_string_equals_ci(obj->ce->name, class_name)) {
			zend_class_entry *expected_class = zend_lookup_class_ex(class_name, NULL, ZEND_FETCH_CLASS_NO_AUTOLOAD);
			if (!expected_class || !instanceof_function(Z_OBJ_P(zv)->ce, expected_class)) {
				return false;
			}
		}
	} else {
		zend_type type = ZEND_TYPE_INIT_MASK(type_ast->attr);
		if (!ZEND_TYPE_CONTAINS_CODE(type, Z_TYPE_P(zv))) {
			return false;
		}
	}

	return true;
}

static bool pattern_matching_bailout(void)
{
	EG(pattern_matching_bailout) = true;
	zend_bailout();
}

static bool match_zval(zval *lhs, zval *rhs)
{
	return zend_is_identical(lhs, rhs);
}

static zend_class_entry *get_class_from_fetch_type(uint32_t fetch_type)
{
	zend_class_entry *scope;

	switch (fetch_type) {
		case ZEND_FETCH_CLASS_SELF:
			scope = zend_get_executed_scope();
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"self\" when no class scope is active");
				pattern_matching_bailout();
			}
			return scope;
		case ZEND_FETCH_CLASS_PARENT:
			scope = zend_get_executed_scope();
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"parent\" when no class scope is active");
				pattern_matching_bailout();
			}
			scope = scope->parent;
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"parent\" when current class scope has no parent");
				pattern_matching_bailout();
			}
			return scope;
		case ZEND_FETCH_CLASS_STATIC:
			scope = zend_get_called_scope(EG(current_execute_data));
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"static\" when no class scope is active");
				pattern_matching_bailout();
			}
			return scope;
		EMPTY_SWITCH_DEFAULT_CASE();
	}

	return NULL;
}

static bool match_object(zval *zv, zend_ast *pattern)
{
	if (Z_TYPE_P(zv) != IS_OBJECT) {
		return false;
	}

	zend_object *obj = Z_OBJ_P(zv);
	zend_ast *class_name_ast = pattern->child[0];
	
	if (class_name_ast) {
		zend_string *class_name = zend_ast_get_str(class_name_ast);
		if (!zend_string_equals_ci(obj->ce->name, class_name)) {
			zend_class_entry *expected_class = zend_lookup_class_ex(class_name, NULL, ZEND_FETCH_CLASS_NO_AUTOLOAD);
			if (!expected_class || !instanceof_function(Z_OBJ_P(zv)->ce, expected_class)) {
				return false;
			}
		}
	} else {
		uint32_t fetch_type = pattern->attr;
		zend_class_entry *scope = get_class_from_fetch_type(fetch_type);
		if (!instanceof_function(Z_OBJ_P(zv)->ce, scope)) {
			return false;
		}
	}

	zend_ast_list *elements = zend_ast_get_list(pattern->child[1]);
	for (uint32_t i = 0; i < elements->children; i++) {
		zend_ast *element = elements->child[i];
		zend_ast *property_or_method_call = element->child[0];
		zend_ast *element_pattern = element->child[1];
		zend_string *property_name = zend_ast_get_str(property_or_method_call);
		zval property_result_rv;
		zval *property_result = obj->handlers->read_property(obj, property_name, BP_VAR_R, NULL, &property_result_rv);
		bool element_matched = zend_pattern_match_ex(property_result, element_pattern);
		if (property_result == &property_result_rv) {
			zval_ptr_dtor(property_result);
		}
		if (!element_matched) {
			return false;
		}
	}

	return true;
}

static bool match_range(zval *zv, zend_ast *pattern)
{
	zval *start = zend_ast_get_zval(pattern->child[0]);
	zval *end = zend_ast_get_zval(pattern->child[1]);

	if (Z_TYPE_P(zv) != Z_TYPE_P(start) && zend_compare(zv, start) == -1) {
		return false;
	}

	int end_check = zend_compare(zv, end);
	if (Z_TYPE_P(zv) != Z_TYPE_P(end) || end_check == 1 || (!(pattern->attr & ZEND_AST_RANGE_INCLUSIVE_END) && end_check == 0)) {
		return false;
	}

	return true;
}

static bool match_binding(zval *zv, zend_ast *pattern)
{
	zend_ast *sub_pattern = pattern->child[1];
	if (sub_pattern && !zend_pattern_match_ex(zv, sub_pattern)) {
		return false;
	}

	// FIXME: Delay to the end of pattern matching
	zend_execute_data *execute_data = EG(current_execute_data);
	uint32_t var = (uint32_t) pattern->attr;
	zval *cv = EX_VAR(var);
	zend_assign_to_variable(cv, zv, IS_CV, EX_USES_STRICT_TYPES());
	/* Destructor might throw */
	if (EG(exception)) {
		pattern_matching_bailout();
	}
	return true;
}

static bool match_array(zval *zv, zend_ast *pattern)
{
	if (Z_TYPE_P(zv) != IS_ARRAY) {
		return false;
	}

	HashTable *ht = Z_ARRVAL_P(zv);
	zend_ast_list *element_list = zend_ast_get_list(pattern->child[0]);

	if (!(pattern->attr & ZEND_ARRAY_PATTERN_NON_EXHAUSTIVE) && element_list->children != zend_hash_num_elements(ht)) {
		return false;
	}

	// FIXME: Deal with indexes properly
	zend_long index = 0;

	for (uint32_t i = 0; i < element_list->children; i++) {
		zend_ast *element = element_list->child[i];
		zend_ast *key_ast = element->child[0];
		zend_ast *pattern_ast = element->child[1];
		zval *element_zv;
		if (key_ast) {
			zval *key_zv = zend_ast_get_zval(key_ast);
			if (Z_TYPE_P(key_zv) == IS_LONG) {
				element_zv = zend_hash_index_find(ht, Z_LVAL_P(key_zv));
			} else {
				ZEND_ASSERT(Z_TYPE_P(key_zv) == IS_STRING);
				element_zv = zend_hash_find(ht, Z_STR_P(key_zv));
			}
		} else {
			element_zv = zend_hash_index_find(ht, index);
			index++;
		}
		if (Z_TYPE_P(element_zv) == IS_UNDEF || !zend_pattern_match_ex(element_zv, pattern_ast)) {
			return false;
		}
	}

	return true;
}

bool zend_pattern_match_ex(zval *zv, zend_ast *pattern)
{
	ZVAL_DEREF(zv);
	// FIXME: Do we need DEINDIRECT too?

	switch (pattern->kind) {
		case ZEND_AST_TYPE_PATTERN:
			return match_type(zv, pattern);
		case ZEND_AST_ZVAL:
			return match_zval(zv, zend_ast_get_zval(pattern));
		case ZEND_AST_OBJECT_PATTERN:
			return match_object(zv, pattern);
		case ZEND_AST_WILDCARD_PATTERN:
			return true;
		case ZEND_AST_OR_PATTERN:
			return zend_pattern_match_ex(zv, pattern->child[0])
				|| zend_pattern_match_ex(zv, pattern->child[1]);
		case ZEND_AST_RANGE_PATTERN:
			return match_range(zv, pattern);
		case ZEND_AST_BINDING_PATTERN:
			return match_binding(zv, pattern);
		case ZEND_AST_ARRAY_PATTERN:
			return match_array(zv, pattern);
		EMPTY_SWITCH_DEFAULT_CASE();
	}
}

bool zend_pattern_match(zval *zv, zend_ast *pattern)
{
	bool result;

	zend_first_try {
		result = zend_pattern_match_ex(zv, pattern);
	} zend_catch {
		if (!EG(pattern_matching_bailout)) {
			zend_bailout();
		}
		ZEND_ASSERT(EG(exception));
		EG(pattern_matching_bailout) = false;
		return false;
	} zend_end_try();

	return result;
}
