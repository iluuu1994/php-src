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

#ifndef ZEND_BIT_ENUM_H
#define ZEND_BIT_ENUM_H

#define _ZEND_BIT_ENUM_CASE(name, value) name = (value),
#define _ZEND_BIT_ENUM_MASK(name, value) (value) |

#define ZEND_BIT_ENUM(name, CASES) \
	typedef enum { \
		CASES(_ZEND_BIT_ENUM_CASE) \
	} name; \
	static const int name##_mask = CASES(_ZEND_BIT_ENUM_MASK) 0; \
	static zend_always_inline name name##_init(int bits) { \
		ZEND_ASSERT((bits & name##_mask) == bits); \
		return (name) bits; \
	}

#endif
