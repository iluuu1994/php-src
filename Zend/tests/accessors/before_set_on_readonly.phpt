--TEST--
Accessor beforeSet hook on readonly property
--FILE--
<?php

class C {
    public $prop {
        get => 42;
        beforeSet {}
    }
}

?>
--EXPECTF--
Fatal error: Virtual readonly property C::$prop must not declare beforeSet hook in %s on line %d
