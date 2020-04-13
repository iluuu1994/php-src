--TEST--
Match expression string jump table
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
        'a' => 'a',
        'b', 'c' => 'b, c',
        'd' => 'd',
        'e', 'f' => 'e, f',
        'g' => 'g',
        'h', 'i' => 'h, i',
    };
}

foreach (range('a', 'i') as $char) {
    var_dump(test($char));
}

--EXPECTF--
$_main:
     ; (lines=%d, args=0, vars=1, tmps=2)
     ; (after optimizer)
     ; %s
0000 INIT_FCALL 2 %d string("range")
0001 SEND_VAL string("a") 1
0002 SEND_VAL string("i") 2
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
     ; (lines=%d, args=1, vars=1, tmps=1)
     ; (after optimizer)
     ; %s
0000 CV0($char) = RECV 1
0001 SWITCH_STRING CV0($char) "a": 0020, "b": 0021, "c": 0021, "d": 0022, "e": 0023, "f": 0023, "g": 0024, "h": 0025, "i": 0025, default: 0026
0002 T1 = IS_IDENTICAL CV0($char) string("a")
0003 JMPNZ T1 0020
0004 T1 = IS_IDENTICAL CV0($char) string("b")
0005 JMPNZ T1 0021
0006 T1 = IS_IDENTICAL CV0($char) string("c")
0007 JMPNZ T1 0021
0008 T1 = IS_IDENTICAL CV0($char) string("d")
0009 JMPNZ T1 0022
0010 T1 = IS_IDENTICAL CV0($char) string("e")
0011 JMPNZ T1 0023
0012 T1 = IS_IDENTICAL CV0($char) string("f")
0013 JMPNZ T1 0023
0014 T1 = IS_IDENTICAL CV0($char) string("g")
0015 JMPNZ T1 0024
0016 T1 = IS_IDENTICAL CV0($char) string("h")
0017 JMPNZ T1 0025
0018 T1 = IS_IDENTICAL CV0($char) string("i")
0019 JMPZNZ T1 0026 0025
0020 RETURN string("a")
0021 RETURN string("b, c")
0022 RETURN string("d")
0023 RETURN string("e, f")
0024 RETURN string("g")
0025 RETURN string("h, i")
0026 V1 = NEW 0 string("UnhandledMatchError")
0027 DO_FCALL
0028 THROW V1
LIVE RANGES:
     1: 0027 - 0028 (new)
string(1) "a"
string(4) "b, c"
string(4) "b, c"
string(1) "d"
string(4) "e, f"
string(4) "e, f"
string(1) "g"
string(4) "h, i"
string(4) "h, i"
