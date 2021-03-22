--TEST--
Test the case where the initializing property assignment fails
--FILE--
<?php
class Test {
    public string $prop { get; }
}
?>
--EXPECTF--
Fatal error: Cannot have implicit get without set in %s on line %d
