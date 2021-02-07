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

#ifndef ZEND_ENUM_H
#define ZEND_ENUM_H

#include "zend.h"
#include "zend_types.h"

void zend_register_enum_ce(void);
void zend_enum_add_interfaces(zend_class_entry *ce);
zend_object *zend_enum_new(zval *result, zend_class_entry *ce, zend_string *case_name, zval *scalar_zv);
void zend_verify_enum(zend_class_entry *ce);
void zend_enum_register_funcs(zend_class_entry *ce);
void zend_enum_register_props(zend_class_entry *ce);
zval *zend_enum_fetch_case_name(zend_object *zobj);
zval *zend_enum_fetch_case_value(zend_object *zobj);

#endif /* ZEND_ENUM_H */
