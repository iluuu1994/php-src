--TEST--
Bug #64960 (Segfault in gc_zval_possible_root)
--FILE--
<?php
// this makes ob_end_clean raise an error
ob_end_flush();

class ExceptionHandler {
    public function __invoke (Exception $e)
    {
        // this triggers the custom error handler
        ob_end_clean();
    }
}

// this must be a class, closure does not trigger segfault
set_exception_handler(new ExceptionHandler());

// exception must be thrown from error handler.
set_error_handler(function()
{
    $e = new Exception;
    $e->_trace = debug_backtrace();

    throw $e;
}, promote_to_exception: true);

// trigger error handler
$a['waa'];
?>
--EXPECTF--
Notice: ob_end_flush(): Failed to delete and flush buffer. No buffer to delete or flush in %sbug64960.php on line 3

Deprecated: Creation of dynamic property Exception::$_trace is deprecated in %s on line %d

Deprecated: Creation of dynamic property Exception::$_trace is deprecated in %s on line %d

Fatal error: Uncaught Exception in %sbug64960.php:19
Stack trace:
#0 %s(%d): {closure:%s:%d}(8, 'ob_end_clean():...', '/home/arnaud/de...', 9)
#1 [internal function]: ExceptionHandler->__invoke(Object(Exception))
#2 {main}
  thrown in %sbug64960.php on line 19
