--TEST--
ZE2 __call() signature check
--FILE--
<?php

class Test {
    function __call() {
    }
}

?>
--EXPECTF--
Fatal error: Method Test::__call() must take between 2 and 3 arguments in %s on line %d
