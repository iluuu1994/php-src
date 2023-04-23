--TEST--
Abstract property with abstract hook is redundant so invalid
--FILE--
<?php

class C {
    abstract public $prop { abstract get; }
}

?>
--EXPECTF--
Fatal error: Accessor on abstract property cannot be explicitly abstract in %s on line %d
