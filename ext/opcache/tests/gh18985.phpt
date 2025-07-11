--TEST--
GH-18985: Wrong lineno for multiline expressions
--EXTENSIONS--
opcache
--INI--
opcache.enable_cli=1
opcache.opt_debug_level=0x40010000
--FILE--
<?php

echo match(15) {
    13 => "A",
    15 => "B",
    default => "C",
};

?>
--EXPECT--
$_main:
     ; (lines=9, args=0, vars=0, tmps=2)
     ; (before optimizer)
     ; /home/ilutov/Developer/php-src/ext/opcache/tests/gh18985.php:1-10
     ; return  [] RANGE[0..0]
L0003 0000 MATCH int(15) 13: 0001, 15: 0003, default: 0005
L0003 0001 T1 = QM_ASSIGN string("A")
L0003 0002 JMP 0007
L0003 0003 T1 = QM_ASSIGN string("B")
L0003 0004 JMP 0007
L0003 0005 T1 = QM_ASSIGN string("C")
L0003 0006 JMP 0007
L0003 0007 ECHO T1
L0010 0008 RETURN int(1)
LIVE RANGES:
     1: 0006 - 0007 (tmp/var)
B
