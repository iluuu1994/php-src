--TEST--
GH-18985: Wrong lineno for multiline expressions
--EXTENSIONS--
opcache
--INI--
opcache.enable_cli=1
opcache.opt_debug_level=0x40010000
--FILE--
<?php

function test() {
    return
        foo()
        +
        bar();
}

?>
--EXPECTF--
$_main:
     ; (lines=1, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_2.php:1-11
     ; return  [] RANGE[0..0]
L0011 0000 RETURN int(1)

test:
     ; (lines=7, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_2.php:3-8
     ; return  [] RANGE[0..0]
L0005 0000 INIT_FCALL_BY_NAME 0 string("foo")
L0005 0001 T0 = DO_FCALL_BY_NAME
L0007 0002 INIT_FCALL_BY_NAME 0 string("bar")
L0007 0003 T1 = DO_FCALL_BY_NAME
L0006 0004 T2 = ADD T0 T1
L0004 0005 RETURN T2
L0008 0006 RETURN null
LIVE RANGES:
     0: 0002 - 0004 (tmp/var)
