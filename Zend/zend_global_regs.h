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

#ifndef ZEND_GLOBAL_REGS_H
#define ZEND_GLOBAL_REGS_H

#ifdef PHP_WIN32
# include "../../../../main/config.w32.h"
#else
# include <php_config.h>
#endif
#include "zend_vm_opcodes.h"

#if defined(HAVE_GCC_GLOBAL_REGS) && ((ZEND_VM_KIND == ZEND_VM_KIND_CALL) || (ZEND_VM_KIND == ZEND_VM_KIND_HYBRID))
# if defined(__GNUC__) && ZEND_GCC_VERSION >= 4008 && defined(i386)
#  define ZEND_VM_IP_GLOBAL_REG "%edi"
# elif defined(__GNUC__) && ZEND_GCC_VERSION >= 4008 && defined(__x86_64__)
#  define ZEND_VM_IP_GLOBAL_REG "%r15"
# elif defined(__GNUC__) && ZEND_GCC_VERSION >= 4008 && defined(__powerpc64__)
#  define ZEND_VM_IP_GLOBAL_REG "r15"
# elif defined(__IBMC__) && ZEND_GCC_VERSION >= 4002 && defined(__powerpc64__)
#  define ZEND_VM_IP_GLOBAL_REG "r15"
# elif defined(__GNUC__) && ZEND_GCC_VERSION >= 4008 && defined(__aarch64__)
#  define ZEND_VM_IP_GLOBAL_REG "x28"
# elif defined(__GNUC__) && ZEND_GCC_VERSION >= 4008 && defined(__riscv) && __riscv_xlen == 64
#  define ZEND_VM_IP_GLOBAL_REG "x19"
# endif
#endif

#if defined(ZEND_UNIVERSAL_GLOBAL_REGS) && defined(ZEND_VM_IP_GLOBAL_REG)
# pragma GCC diagnostic ignored "-Wvolatile-register-var"
register const zend_op* volatile opline __asm__(ZEND_VM_IP_GLOBAL_REG);
# pragma GCC diagnostic warning "-Wvolatile-register-var"
#endif

#endif /* ZEND_GLOBAL_REGS_H */
