--TEST--
Bug #28213 (crash in debug_print_backtrace in static methods)
--FILE--
<?php
class FooBar { static function error() { debug_print_backtrace(); } }
set_error_handler(array('FooBar', 'error'));
include('foobar.php');
?>
--EXPECT--
#0 [internal function]: FooBar::error(2, 'include(foobar....', '/home/arnaud/de...', 4)
#0 [internal function]: FooBar::error(2, 'include(): Fail...', '/home/arnaud/de...', 4)
