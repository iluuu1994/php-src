--TEST--
Bug #45805 (Crash on throwing exception from error handler)
--FILE--
<?php
class PHPUnit_Util_ErrorHandler
{
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new RuntimeException;
    }
}

class A {
    public function getX() {
        return NULL;
    }
}

class B {
    public function foo() {
        $obj    = new A;
        $source = &$obj->getX();
    }

    public function bar() {
        $m = new ReflectionMethod('B', 'foo');
        $m->invoke($this);
    }
}

set_error_handler(
  array('PHPUnit_Util_ErrorHandler', 'handleError'), E_ALL
);

$o = new B;
$o->bar();
?>
--EXPECTF--
Fatal error: Uncaught RuntimeException in %sbug45805.php:%d
Stack trace:
#0 [internal function]: PHPUnit_Util_ErrorHandler::handleError(8, 'Only variables ...', '/home/arnaud/de...', 19)
#1 %s(%d): ReflectionMethod->invoke(Object(B))
#2 %s(%d): B->bar()
#3 {main}
  thrown in %sbug45805.php on line %d
