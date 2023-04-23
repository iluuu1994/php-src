--TEST--
Defining a get and beforeSet hook with no set hook is illegal
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
Fatal error: Virtual read-only property C::$prop must not declare beforeSet hook in %s on line %d
