--TEST--
Unknown property hook
--FILE--
<?php

class Test {
    public $prop {
        foobar {}
    }
}

?>
--EXPECTF--
Fatal error: Unknown hook "foobar" for property Test::$prop, expected "get", "set" or "beforeSet" in %s on line %d
