--TEST--
Test result of match cannot be modified by reference
--SKIPIF--
<?php if (!extension_loaded('Zend OPcache')) die('skip Zend OPcache extension not available'); ?>
--INI--
opcache.opt_debug_level=0x20000
opcache.enable=1
opcache.enable_cli=1
--FILE--
<?php

// opcache can't be certain Test::usesRef is actually this method
if (!class_exists('Test')) {
    class Test {
        public static function usesRef(&$x) {
            $x = 'modified';
        }
        public static function usesValue($x) {
            echo "usesValue $x\n";
        }
    }
}

function main() {
    $i = 0;
    Test::usesValue(match(true) { true => $i });
    echo "i is $i\n";
    $j = 1;
    Test::usesRef(match(true) { true => $i });
    echo "j is $j\n";
}
main();

--EXPECTF--
$_main:
     ; (lines=8, args=0, vars=0, tmps=1)
     ; (after optimizer)
     ; %s027.php:1-25
0000 INIT_FCALL 1 %d string("class_exists")
0001 SEND_VAL string("Test") 1
0002 V0 = DO_ICALL
0003 JMPNZ V0 0005
0004 DECLARE_CLASS string("test")
0005 INIT_FCALL 0 %d string("main")
0006 DO_UCALL
0007 RETURN int(1)

main:
     ; (lines=9, args=0, vars=0, tmps=0)
     ; (after optimizer)
     ; %s027.php:15-22
0000 INIT_STATIC_METHOD_CALL 1 string("Test") string("usesValue")
0001 SEND_VAL_EX int(0) 1
0002 DO_FCALL
0003 ECHO string("i is 0
")
0004 INIT_STATIC_METHOD_CALL 1 string("Test") string("usesRef")
0005 SEND_VAL_EX int(0) 1
0006 DO_FCALL
0007 ECHO string("j is 1
")
0008 RETURN null

Test::usesRef:
     ; (lines=3, args=1, vars=1, tmps=0)
     ; (after optimizer)
     ; %s027.php:6-8
0000 CV0($x) = RECV 1
0001 ASSIGN CV0($x) string("modified")
0002 RETURN null

Test::usesValue:
     ; (lines=6, args=1, vars=1, tmps=%d)
     ; (after optimizer)
     ; %s027.php:9-11
0000 CV0($x) = RECV 1
0001 T2 = ROPE_INIT 3 string("usesValue ")
0002 T2 = ROPE_ADD 1 T2 CV0($x)
0003 T1 = ROPE_END 2 T2 string("
")
0004 ECHO T1
0005 RETURN null
LIVE RANGES:
     2: 0001 - 0003 (rope)
usesValue 0
i is 0

Fatal error: Uncaught Error: Cannot pass parameter 1 by reference in %s027.php:20
Stack trace:
#0 %s027.php(23): main()
#1 {main}
  thrown in %s027.php on line 20
