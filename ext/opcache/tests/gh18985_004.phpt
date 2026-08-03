--TEST--
GH-18985: Wrong lineno for multiline expressions
--EXTENSIONS--
opcache
--INI--
opcache.enable_cli=1
opcache.opt_debug_level=0x40010000
--FILE--
<?php

switch (1) {
    case 1: echo 1;
    case 2: echo 2;
    case 3: echo 3;
}

?>
--EXPECTF--
$_main:
     ; (lines=11, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_004.php:1-10
     ; return  [] RANGE[0..0]
L0004 0000 T0 = IS_EQUAL int(1) int(1)
L0004 0001 JMPNZ T0 0007
L0005 0002 T0 = IS_EQUAL int(1) int(2)
L0005 0003 JMPNZ T0 0008
L0006 0004 T0 = IS_EQUAL int(1) int(3)
L0006 0005 JMPNZ T0 0009
L0006 0006 JMP 0010
L0004 0007 ECHO int(1)
L0005 0008 ECHO int(2)
L0006 0009 ECHO int(3)
L0010 0010 RETURN int(1)
123
