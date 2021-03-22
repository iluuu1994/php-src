--TEST--
Accessor cannot be both abstract and final
--FILE--
<?php

class Test {
    public $prop { abstract final get; }
}

?>
--EXPECTF--
Fatal error: Accessor cannot be both abstract and final in %s on line %d
