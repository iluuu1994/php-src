--TEST--
GH-21699 (static::): no shutdown_executor trampoline assertion when error handler throws during static:: callable resolution
--FILE--
<?php
set_error_handler(function ($_, $errstr) {
    throw new Exception($errstr);
}, delay: false);
class bar {
    public static function __callstatic($fusion, $b)
    {
    }
    public function test()
    {
        call_user_func('static::y');
    }
}
$x = new bar;
$x->test();
?>
--EXPECTF--
Fatal error: Uncaught Exception: Use of "static" in callables is deprecated in %s:%d
Stack trace:
#0 %s(%d): {closure:%s}(%d, 'Use of "static"%s', '%s', %d)
#1 %s(%d): bar->test()
#2 {main}
  thrown in %s on line %d
