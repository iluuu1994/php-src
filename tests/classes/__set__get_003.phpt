--TEST--
ZE2 __set() signature check
--FILE--
<?php
class Test {
    function __set() {
    }
}

?>
--EXPECTF--
Fatal error: Method Test::__set() must take between 2 and 3 arguments in %s on line %d
