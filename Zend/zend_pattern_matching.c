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
#include "zend_API.h"
#include "zend_compile.h"
#include "zend_execute.h"
#include "zend_exceptions.h"
#include "zend_type_info.h"
#include "zend_constants.h"

typedef enum {
	PM_ERROR = -1,
	PM_MISMATCH = 0,
	PM_MATCH = 1,
} pm_result;

typedef struct {
	zend_array *bindings;
} pm_context;

pm_result zend_pattern_match_ex(zval *zv, zend_ast *pattern, pm_context *context);
static zend_class_entry *get_class_from_fetch_type(uint32_t fetch_type);

static pm_result match_type(zval *zv, zend_ast *type_ast)
{
	zend_ast *class_name_ast = type_ast->child[0];
	uint32_t fetch_type;

	if (class_name_ast) {
		if (Z_TYPE_P(zv) != IS_OBJECT) {
			return PM_MISMATCH;
		}

		zend_string *class_name = zend_ast_get_str(class_name_ast);
		fetch_type = zend_get_class_fetch_type(class_name);

		zend_class_entry *expected_class = NULL;
		if (fetch_type == ZEND_FETCH_CLASS_DEFAULT) {
			zend_object *obj = Z_OBJ_P(zv);
			if (zend_string_equals_ci(obj->ce->name, class_name)) {
				return PM_MATCH;
			}
			expected_class = zend_lookup_class_ex(class_name, NULL, ZEND_FETCH_CLASS_NO_AUTOLOAD);
		} else {
non_default_fetch_type:
			expected_class = get_class_from_fetch_type(fetch_type);
			if (!expected_class) {
				return PM_ERROR;
			}
		}
		if (!expected_class || !instanceof_function(Z_OBJ_P(zv)->ce, expected_class)) {
			return PM_MISMATCH;
		}
	} else {
		zend_type type = ZEND_TYPE_INIT_MASK(type_ast->attr);
		if (!ZEND_TYPE_CONTAINS_CODE(type, Z_TYPE_P(zv))) {
			if (ZEND_TYPE_CONTAINS_CODE(type, IS_STATIC)) {
				fetch_type = ZEND_FETCH_CLASS_STATIC;
				goto non_default_fetch_type;
			}
			return PM_MISMATCH;
		}
	}

	return PM_MATCH;
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
				return NULL;
			}
			return scope;
		case ZEND_FETCH_CLASS_PARENT:
			scope = zend_get_executed_scope();
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"parent\" when no class scope is active");
				return NULL;
			}
			scope = scope->parent;
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"parent\" when current class scope has no parent");
				return NULL;
			}
			return scope;
		case ZEND_FETCH_CLASS_STATIC:
			scope = zend_get_called_scope(EG(current_execute_data));
			if (UNEXPECTED(!scope)) {
				zend_throw_error(NULL, "%s", "Cannot access \"static\" when no class scope is active");
				return NULL;
			}
			return scope;
		EMPTY_SWITCH_DEFAULT_CASE();
	}

	return NULL;
}

static pm_result match_object(zval *zv, zend_ast *pattern, pm_context *context)
{
	if (Z_TYPE_P(zv) != IS_OBJECT) {
		return PM_MISMATCH;
	}

	zend_object *obj = Z_OBJ_P(zv);
	zend_ast_list *elements = zend_ast_get_list(pattern->child[0]);
	for (uint32_t i = 0; i < elements->children; i++) {
		zend_ast *element = elements->child[i];
		zend_ast *property_or_method_call = element->child[0];
		zend_ast *element_pattern = element->child[1];
		zend_string *property_name = zend_ast_get_str(property_or_method_call);
		zval property_result_rv;
		zval *property_result = obj->handlers->read_property(obj, property_name, BP_VAR_R, NULL, &property_result_rv);
		pm_result element_matched = zend_pattern_match_ex(property_result, element_pattern, context);
		if (property_result == &property_result_rv) {
			zval_ptr_dtor(property_result);
		}
		if (element_matched != PM_MATCH) {
			return element_matched;
		}
	}

	return PM_MATCH;
}

static pm_result match_range(zval *zv, zend_ast *pattern)
{
	zval *start = zend_ast_get_zval(pattern->child[0]);
	zval *end = zend_ast_get_zval(pattern->child[1]);

	if (Z_TYPE_P(zv) != Z_TYPE_P(start) && zend_compare(zv, start) == -1) {
		return PM_MISMATCH;
	}

	int end_check = zend_compare(zv, end);
	if (Z_TYPE_P(zv) != Z_TYPE_P(end) || end_check == 1 || (!(pattern->attr & ZEND_AST_RANGE_INCLUSIVE_END) && end_check == 0)) {
		return PM_MISMATCH;
	}

	return PM_MATCH;
}

static pm_result match_binding(zval *zv, zend_ast *pattern, pm_context *context)
{
	ZVAL_DEREF(zv);
	Z_TRY_ADDREF_P(zv);
	zend_hash_index_add(context->bindings, (zend_ulong) pattern->attr, zv);
	return PM_MATCH;
}

static pm_result match_array(zval *zv, zend_ast *pattern, pm_context *context)
{
	if (Z_TYPE_P(zv) != IS_ARRAY) {
		return PM_MISMATCH;
	}

	HashTable *ht = Z_ARRVAL_P(zv);
	zend_ast_list *element_list = zend_ast_get_list(pattern->child[0]);

	if (pattern->attr & ZEND_ARRAY_PATTERN_NON_EXHAUSTIVE) {
		if (element_list->children > zend_hash_num_elements(ht)) {
			return PM_MISMATCH;
		}
	} else {
		if (element_list->children != zend_hash_num_elements(ht)) {
			return PM_MISMATCH;
		}
	}

	/* Explicit and implicit keys may not be mixed, so there's no need to
	 * replicate array key sequencing logic. */
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
		if (!element_zv || Z_TYPE_P(element_zv) == IS_UNDEF || !zend_pattern_match_ex(element_zv, pattern_ast, context)) {
			return PM_MISMATCH;
		}
	}

	return PM_MATCH;
}

pm_result match_class_const(zval *zv, zend_ast *pattern)
{
	zend_string *class_name = !pattern->attr
		? zend_ast_get_str(pattern->child[0])
		: NULL;
	zend_string *constant_name = zend_ast_get_str(pattern->child[1]);
	zval *constant_zv = zend_get_class_constant_ex(class_name, constant_name, zend_get_executed_scope(), pattern->attr);
	if (EG(exception)) {
		return PM_ERROR;
	}
	return zend_is_identical(zv, constant_zv) ? PM_MATCH : PM_MISMATCH;
}

pm_result zend_pattern_match_ex(zval *zv, zend_ast *pattern, pm_context *context)
{
	ZVAL_DEREF(zv);

	switch (pattern->kind) {
		case ZEND_AST_TYPE_PATTERN:
			return match_type(zv, pattern);
		case ZEND_AST_ZVAL:
			return match_zval(zv, zend_ast_get_zval(pattern));
		case ZEND_AST_OBJECT_PATTERN:
			return match_object(zv, pattern, context);
		case ZEND_AST_WILDCARD_PATTERN:
			return PM_MATCH;
		case ZEND_AST_OR_PATTERN: {
			pm_result lhs = zend_pattern_match_ex(zv, pattern->child[0], context);
			if (lhs != PM_MISMATCH) {
				return lhs;
			}
			return zend_pattern_match_ex(zv, pattern->child[1], context);
		}
		case ZEND_AST_AND_PATTERN: {
			pm_result lhs = zend_pattern_match_ex(zv, pattern->child[0], context);
			if (lhs != PM_MATCH) {
				return lhs;
			}
			return zend_pattern_match_ex(zv, pattern->child[1], context);
		}
		case ZEND_AST_RANGE_PATTERN:
			return match_range(zv, pattern);
		case ZEND_AST_BINDING_PATTERN:
			return match_binding(zv, pattern, context);
		case ZEND_AST_ARRAY_PATTERN:
			return match_array(zv, pattern, context);
		case ZEND_AST_CLASS_CONST_PATTERN:
			return match_class_const(zv, pattern);
		EMPTY_SWITCH_DEFAULT_CASE();
	}
}

bool zend_pattern_match(zval *zv, zend_ast *pattern, zend_array *bindings)
{
	pm_context context = { .bindings = bindings };
	pm_result result = zend_pattern_match_ex(zv, pattern, &context);
	return result == PM_MATCH;
}
