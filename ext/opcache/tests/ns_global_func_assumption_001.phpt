--TEST--
Test
--EXTENSIONS--
opcache
--INI--
opcache.enable=1
opcache.enable_cli=1
opcache.opt_debug_level=0x20000
--FILE--
<?php

namespace Ns;

function test() {
    var_dump(strlen('Hello world'));
}

test();
require __DIR__ . '/ns_global_func_assumption_001.inc';
test();

?>
--EXPECTF--
$_main:
     ; (lines=6, args=0, vars=0, tmps=%d)
     ; (after optimizer)
     ; %sns_global_func_assumption_001.php:%s
0000 INIT_FCALL 0 %d string("ns\\test")
0001 DO_UCALL
0002 INCLUDE_OR_EVAL (require) string("%sns_global_func_assumption_001.inc")
0003 INIT_FCALL 0 %d string("ns\\test")
0004 DO_UCALL
0005 RETURN int(1)

Ns\test:
     ; (lines=4, args=0, vars=0, tmps=%d)
     ; (after optimizer)
     ; %sns_global_func_assumption_001.php:%s
0000 INIT_FCALL 1 %d string("var_dump")
0001 SEND_VAL int(11) 1
0002 DO_ICALL
0003 RETURN null
int(11)

$_main:
     ; (lines=1, args=0, vars=0, tmps=%d)
     ; (after optimizer)
     ; %sns_global_func_assumption_001.inc:%s
0000 RETURN int(1)

Ns\strlen:
     ; (lines=4, args=1, vars=1, tmps=%d)
     ; (after optimizer)
     ; %sns_global_func_assumption_001.inc:%s
0000 CV0($string) = RECV 1
0001 T2 = STRLEN CV0($string)
0002 T1 = ADD T2 T2
0003 RETURN T1

$_main:
     ; (lines=6, args=0, vars=0, tmps=%d)
     ; (after optimizer)
     ; %sns_global_func_assumption_001.php:%s
0000 INIT_FCALL 0 %d string("ns\\test")
0001 DO_UCALL
0002 INCLUDE_OR_EVAL (require) string("%sns_global_func_assumption_001.inc")
0003 INIT_FCALL 0 %d string("ns\\test")
0004 DO_UCALL
0005 RETURN int(1)

Ns\test:
     ; (lines=7, args=0, vars=0, tmps=%d)
     ; (after optimizer)
     ; %sns_global_func_assumption_001.php:%s
0000 INIT_NS_FCALL_BY_NAME 1 string("Ns\\var_dump")
0001 INIT_NS_FCALL_BY_NAME 1 string("Ns\\strlen")
0002 SEND_VAL_EX string("Hello world") 1
0003 V0 = DO_FCALL_BY_NAME
0004 SEND_VAR_NO_REF_EX V0 1
0005 DO_FCALL_BY_NAME
0006 RETURN null
int(22)
