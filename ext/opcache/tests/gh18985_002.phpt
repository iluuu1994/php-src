--TEST--
GH-18985: Wrong lineno for multiline expressions
--EXTENSIONS--
opcache
--INI--
opcache.enable_cli=1
opcache.opt_debug_level=0x40010000
--FILE--
<?php

do {
    echo "Foo";
} while(0);

?>
--EXPECTF--
$_main:
     ; (lines=3, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_002.php:1-8
     ; return  [] RANGE[0..0]
L0004 0000 ECHO string("Foo")
L0005 0001 JMPNZ int(0) 0000
L0008 0002 RETURN int(1)
Foo
