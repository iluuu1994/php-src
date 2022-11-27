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

#ifndef ZEND_PATTERN_MATCHING_H
#define ZEND_PATTERN_MATCHING_H

#include "zend.h"

#define ZEND_PM_BINDINGS_SLOTS 8

typedef struct {
	uint32_t var;
	zval value;
} zend_pm_binding;

typedef struct _zend_pm_bindings zend_pm_bindings;
struct _zend_pm_bindings {
	zend_pm_binding list[ZEND_PM_BINDINGS_SLOTS];
	zend_pm_bindings *next;
	uint8_t num_used;
};

typedef struct _zend_pm_context zend_pm_context;
typedef struct _zend_pm_context {
	zend_pm_bindings *bindings;
	zend_pm_bindings bindings_spare;
	zend_pm_context *prev;
} zend_pm_context;

bool zend_pattern_match(zval *zv, zend_ast *pattern);
void zend_pm_contexts_free(void);

#endif
