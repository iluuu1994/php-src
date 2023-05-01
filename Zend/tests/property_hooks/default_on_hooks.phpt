--TEST--
Hooked property cannot have default value
--FILE--
<?php

class TestParent {
    public $prop;
}

class Test extends TestParent {
    public $prop = 0 {
        get {}
        set {}
    }
}

?>
--EXPECTF--
Fatal error: Cannot specify default value for hooked property in %s on line %d
