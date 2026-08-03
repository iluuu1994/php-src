--TEST--
GH-18985: Wrong lineno for multiline expressions
--EXTENSIONS--
opcache
--INI--
opcache.enable_cli=1
opcache.opt_debug_level=0x40010000
--FILE--
<?php

function foo() {}
function bar() {}

foo(
    1,
    2,
    bar(
        3,
        4,
    ),
);

?>
--EXPECTF--
$_main:
     ; (lines=10, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_003.php:1-16
     ; return  [] RANGE[0..0]
L0006 0000 INIT_FCALL 3 128 string("foo")
L0007 0001 SEND_VAL int(1) 1
L0008 0002 SEND_VAL int(2) 2
L0009 0003 INIT_FCALL 2 112 string("bar")
L0010 0004 SEND_VAL int(3) 1
L0011 0005 SEND_VAL int(4) 2
L0009 0006 T0 = DO_UCALL
L0009 0007 SEND_VAL T0 3
L0006 0008 DO_UCALL
L0016 0009 RETURN int(1)

foo:
     ; (lines=1, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_003.php:3-3
     ; return  [] RANGE[0..0]
L0003 0000 RETURN null

bar:
     ; (lines=1, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_003.php:4-4
     ; return  [] RANGE[0..0]
L0004 0000 RETURN null
