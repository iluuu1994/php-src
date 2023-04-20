--TEST--
Purely virtual accessors cannot have default value
--FILE--
<?php

class Test {
    public $prop = 0 {
        get {}
        set {}
    }
}

?>
--EXPECTF--
Fatal error: Cannot specify default value for property with accessors in %s on line %d
