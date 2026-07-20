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
    $foo['bar']['baz']
        +=
        bar();
}

?>
--EXPECTF--
$_main:
     ; (lines=1, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_5.php:1-10
     ; return  [] RANGE[0..0]
L0010 0000 RETURN int(1)

test:
     ; (lines=6, args=0, vars=1, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_5.php:3-7
     ; return  [] RANGE[0..0]
L0006 0000 INIT_FCALL_BY_NAME 0 string("bar")
L0006 0001 T3 = DO_FCALL_BY_NAME
L0004 0002 V1 = FETCH_DIM_RW CV0($foo) string("bar")
L0005 0003 ASSIGN_DIM_OP (ADD) V1 string("baz")
L0005 0004 OP_DATA T3
L0007 0005 RETURN null
LIVE RANGES:
     3: 0002 - 0003 (tmp/var)
