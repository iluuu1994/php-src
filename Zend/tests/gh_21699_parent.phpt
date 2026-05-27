--TEST--
GH-21699 (parent::): no shutdown_executor trampoline assertion when error handler throws during parent:: callable resolution
--FILE--
<?php
set_error_handler(function ($_, $errstr) {
    throw new Exception($errstr);
}, delay: false);
class Base {
    public static function __callStatic($name, $args)
    {
    }
}
class Child extends Base {
    public function test()
    {
        call_user_func('parent::missing');
    }
}
(new Child)->test();
?>
--EXPECTF--
Fatal error: Uncaught Exception: Use of "parent" in callables is deprecated in %s:%d
Stack trace:
#0 %s(%d): {closure:%s}(%d, 'Use of "parent"%s', '%s', %d)
#1 %s(%d): Child->test()
#2 {main}
  thrown in %s on line %d
