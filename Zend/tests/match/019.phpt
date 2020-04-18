--TEST--
Match expression long jump table
--INI--
opcache.enable=1
opcache.enable_cli=1
opcache.opt_debug_level=0x20000
--SKIPIF--
<?php if (!extension_loaded('Zend OPcache')) die('skip Zend OPcache extension not available'); ?>
--FILE--
<?php

function test($char) {
    return match ($char) {
        1 => '1',
        2, 3 => '2, 3',
        4 => '4',
        5, 6 => '5, 6',
        7 => '7',
        8, 9 => '8, 9',
        default => 'default'
    };
}

foreach (range(0, 10) as $char) {
    var_dump(test($char));
}

--EXPECTF--
$_main:
     ; (lines=15, args=0, vars=1, tmps=2)
     ; (after optimizer)
     ; %s
0000 INIT_FCALL 2 %d string("range")
0001 SEND_VAL int(0) 1
0002 SEND_VAL int(10) 2
0003 V2 = DO_ICALL
0004 V1 = FE_RESET_R V2 0013
0005 FE_FETCH_R V1 CV0($char) 0013
0006 INIT_FCALL 1 %d string("var_dump")
0007 INIT_FCALL 1 %d string("test")
0008 SEND_VAR CV0($char) 1
0009 V2 = DO_UCALL
0010 SEND_VAR V2 1
0011 DO_ICALL
0012 JMP 0005
0013 FE_FREE V1
0014 RETURN int(1)
LIVE RANGES:
     1: 0005 - 0013 (loop)

test:
     ; (lines=27, args=1, vars=1, tmps=1)
     ; (after optimizer)
     ; %s
0000 CV0($char) = RECV 1
0001 MATCH_LONG CV0($char) 1: 0020, 2: 0021, 3: 0021, 4: 0022, 5: 0023, 6: 0023, 7: 0024, 8: 0025, 9: 0025, default: 0026
0002 T1 = IS_IDENTICAL CV0($char) int(1)
0003 JMPNZ T1 0020
0004 T1 = IS_IDENTICAL CV0($char) int(2)
0005 JMPNZ T1 0021
0006 T1 = IS_IDENTICAL CV0($char) int(3)
0007 JMPNZ T1 0021
0008 T1 = IS_IDENTICAL CV0($char) int(4)
0009 JMPNZ T1 0022
0010 T1 = IS_IDENTICAL CV0($char) int(5)
0011 JMPNZ T1 0023
0012 T1 = IS_IDENTICAL CV0($char) int(6)
0013 JMPNZ T1 0023
0014 T1 = IS_IDENTICAL CV0($char) int(7)
0015 JMPNZ T1 0024
0016 T1 = IS_IDENTICAL CV0($char) int(8)
0017 JMPNZ T1 0025
0018 T1 = IS_IDENTICAL CV0($char) int(9)
0019 JMPZNZ T1 0026 0025
0020 RETURN string("1")
0021 RETURN string("2, 3")
0022 RETURN string("4")
0023 RETURN string("5, 6")
0024 RETURN string("7")
0025 RETURN string("8, 9")
0026 RETURN string("default")
string(7) "default"
string(1) "1"
string(4) "2, 3"
string(4) "2, 3"
string(1) "4"
string(4) "5, 6"
string(4) "5, 6"
string(1) "7"
string(4) "8, 9"
string(4) "8, 9"
string(7) "default"
