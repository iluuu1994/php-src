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
  | Author: Ilija Tovilo <ilutov@php.net>                                |
  +----------------------------------------------------------------------+
*/

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "php_iterator.h"
#include "iterator_arginfo.h"
#include "zend_interfaces.h"

#define RANGE_ITER_PROP_FROM    0
#define RANGE_ITER_PROP_TO      1
#define RANGE_ITER_PROP_KEY     2
#define RANGE_ITER_PROP_CURRENT 3

static PHP_MINFO_FUNCTION(iterator);

ZEND_DECLARE_MODULE_GLOBALS(iterator)

PHPAPI zend_class_entry *iterator_ce_Iterator_RangeIterator;

static PHP_MINIT_FUNCTION(iterator)
{
	iterator_ce_Iterator_RangeIterator = register_class_Iterator_RangeIterator(zend_ce_iterator);

	return SUCCESS;
}

static PHP_GINIT_FUNCTION(iterator)
{
#if defined(COMPILE_DL_ITERATOR) && defined(ZTS)
	ZEND_TSRMLS_CACHE_UPDATE();
#endif
}

static PHP_RINIT_FUNCTION(iterator)
{
	return SUCCESS;
}

zend_module_entry iterator_module_entry = {
	STANDARD_MODULE_HEADER,
	"iterator",
	ext_functions,
	PHP_MINIT(iterator),
	NULL,
	PHP_RINIT(iterator),
	NULL,
	PHP_MINFO(iterator),
	PHP_ITERATOR_VERSION,
	PHP_MODULE_GLOBALS(iterator),
	PHP_GINIT(iterator),
	NULL,
	NULL,
	STANDARD_MODULE_PROPERTIES_EX
};

#ifdef COMPILE_DL_ITERATOR
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(iterator)
#endif

static PHP_MINFO_FUNCTION(iterator)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "iterator support", "enabled");
	php_info_print_table_end();
}

PHP_FUNCTION(Iterator_range)
{
	zend_long from, to;

	ZEND_PARSE_PARAMETERS_START(2, 2)
		Z_PARAM_LONG(from)
		Z_PARAM_LONG(to)
	ZEND_PARSE_PARAMETERS_END();

	zval object_zv;
	object_init_ex(&object_zv, iterator_ce_Iterator_RangeIterator);
	zend_object *object = Z_OBJ(object_zv);

	ZVAL_LONG(OBJ_PROP_NUM(object, RANGE_ITER_PROP_FROM), from);
	ZVAL_LONG(OBJ_PROP_NUM(object, RANGE_ITER_PROP_TO), to);
	ZVAL_LONG(OBJ_PROP_NUM(object, RANGE_ITER_PROP_KEY), 0);
	ZVAL_LONG(OBJ_PROP_NUM(object, RANGE_ITER_PROP_CURRENT), from);

	RETURN_OBJ(object);
}

PHP_METHOD(Iterator_RangeIterator, __construct)
{
	zend_throw_error(NULL, "Cannot directly construct %s, use iterator functions instead", ZSTR_VAL(Z_OBJCE_P(ZEND_THIS)->name));
}

PHP_METHOD(Iterator_RangeIterator, current)
{
	zend_object *obj = Z_OBJ_P(ZEND_THIS);
	zval *current = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_CURRENT);
	RETURN_COPY_VALUE(current);
}

PHP_METHOD(Iterator_RangeIterator, key)
{
	zend_object *obj = Z_OBJ_P(ZEND_THIS);
	zval *key = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_KEY);
	RETURN_COPY_VALUE(key);
}

PHP_METHOD(Iterator_RangeIterator, next)
{
	zend_object *obj = Z_OBJ_P(ZEND_THIS);

	zval *key = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_KEY);
	ZVAL_LONG(key, Z_LVAL_P(key) + 1);

	zval *current = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_CURRENT);
	ZVAL_LONG(current, Z_LVAL_P(current) + 1);
}

PHP_METHOD(Iterator_RangeIterator, rewind)
{
	zend_object *obj = Z_OBJ_P(ZEND_THIS);

	ZVAL_LONG(OBJ_PROP_NUM(obj, RANGE_ITER_PROP_KEY), 0);

	zval *current = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_CURRENT);
	zval *from = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_FROM);
	ZVAL_COPY_VALUE(current, from);
}

PHP_METHOD(Iterator_RangeIterator, valid)
{
	zend_object *obj = Z_OBJ_P(ZEND_THIS);

	zval *to = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_TO);
	zval *current = OBJ_PROP_NUM(obj, RANGE_ITER_PROP_CURRENT);

	RETURN_BOOL(Z_LVAL_P(current) <= Z_LVAL_P(to));
}
