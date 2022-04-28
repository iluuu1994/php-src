--TEST--
Abstract property without accessor is illegal
--FILE--
<?php

class C {
    abstract public $prop;
}

?>
--EXPECTF--
Fatal error: Only accessor properties may be declared abstract in %s on line %d
