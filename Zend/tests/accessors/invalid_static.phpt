--TEST--
Accessor method cannot be static
--FILE--
<?php

class Test {
    public $prop {
        static get {}
    }
}

?>
--EXPECTF--
Fatal error: Cannot use the static modifier on an accessor in %s on line %d
