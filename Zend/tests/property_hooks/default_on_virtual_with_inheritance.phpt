--TEST--
Virtual property cannot have default value
--FILE--
<?php

class A {
    private $prop;
}

class B extends A {
    public $prop = 0 {
        get {}
        set {}
    }
}

?>
--EXPECTF--
Fatal error: Cannot specify default value for hooked property B::$prop in %s on line %d
