--TEST--
Abstract property hook cannot have body
--FILE--
<?php

class Test {
    public $prop {
        abstract get {}
    }
}

?>
--EXPECTF--
Fatal error: Abstract property hook cannot have body in %s on line %d
