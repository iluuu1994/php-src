--TEST--
Cannot have implicit get without set
--FILE--
<?php

class Test {
    public $prop { get; }
}

?>
--EXPECTF--
Fatal error: Cannot have implicit get without set in %s on line %d
