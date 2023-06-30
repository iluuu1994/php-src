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

#ifndef ZEND_FRAMELESS_FUNCTION_H
#define ZEND_FRAMELESS_FUNCTION_H

#include "stdint.h"

#define DIRECT_FUNCTION_PARAMETERS_0 zval *return_value
#define DIRECT_FUNCTION_PARAMETERS_1 zval *return_value, zval *arg1
#define DIRECT_FUNCTION_PARAMETERS_2 zval *return_value, zval *arg1, zval *arg2
#define DIRECT_FUNCTION_PARAMETERS_3 zval *return_value, zval *arg1, zval *arg2, zval *arg3

#define ZEND_FRAMELESS_FUNCTION_NAME(name, arity) zdc_##name##_##arity

#define ZEND_FRAMELESS_FUNCTION(name, arity) \
	void ZEND_FRAMELESS_FUNCTION_NAME(name, arity)(DIRECT_FUNCTION_PARAMETERS_##arity)

typedef struct _zval_struct zval;

typedef void (*zend_frameless_function_0)(zval *return_value);
typedef void (*zend_frameless_function_1)(zval *return_value, zval *op1);
typedef void (*zend_frameless_function_2)(zval *return_value, zval *op1, zval *op2);
typedef void (*zend_frameless_function_3)(zval *return_value, zval *op1, zval *op2, zval *op3);

extern const zend_frameless_function_0 zend_frameless_function_0_list[];
extern const zend_frameless_function_1 zend_frameless_function_1_list[];
extern const zend_frameless_function_2 zend_frameless_function_2_list[];
extern const zend_frameless_function_3 zend_frameless_function_3_list[];

typedef struct {
	void *handler;
	uint32_t num_args;
} zend_frameless_function_info;

#endif
