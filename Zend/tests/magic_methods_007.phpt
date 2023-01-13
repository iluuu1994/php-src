--TEST--
Testing __set() declaration in abstract class with wrong modifier
--FILE--
<?php

abstract class b {
    abstract protected function __set($a);
}

?>
--EXPECTF--
Fatal error: Method b::__set() must take between 2 and 3 arguments in %s on line %d
