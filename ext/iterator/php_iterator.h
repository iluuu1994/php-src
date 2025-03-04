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

#ifndef PHP_ITERATOR_H
#define PHP_ITERATOR_H

#include "php_version.h"

#define PHP_ITERATOR_VERSION PHP_VERSION

#ifdef ZTS
#include "TSRM.h"
#endif

extern PHPAPI zend_class_entry *iterator_ce_Iterator_RangeIterator;

extern zend_module_entry iterator_module_entry;
#define iterator_module_ptr &iterator_module_entry

ZEND_BEGIN_MODULE_GLOBALS(iterator)
ZEND_END_MODULE_GLOBALS(iterator)

ZEND_EXTERN_MODULE_GLOBALS(iterator)
#define ITER_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(iterator, v)

#if defined(ZTS) && defined(COMPILE_DL_ITERATOR)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

#define phpext_iterator_ptr iterator_module_ptr

#endif  /* PHP_ITERATOR_H */
