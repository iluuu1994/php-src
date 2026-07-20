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
    switch (1) {
        case 1:
            break
                1;
    }
}

?>
--EXPECTF--
$_main:
     ; (lines=1, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_3.php:1-12
     ; return  [] RANGE[0..0]
L0012 0000 RETURN int(1)

test:
     ; (lines=5, args=0, vars=0, tmps=%d)
     ; (before optimizer)
     ; %sgh18985_3.php:3-9
     ; return  [] RANGE[0..0]
L0004 0000 T0 = IS_EQUAL int(1) int(1)
L0004 0001 JMPNZ T0 0003
L0004 0002 JMP 0004
L0007 0003 JMP 0004
L0009 0004 RETURN null
