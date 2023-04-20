--TEST--
Accessor cannot have explicity visibility
--FILE--
<?php

class Test {
    private $prop {
        public get;
    }
}

?>
--EXPECTF--
Fatal error: Cannot use the public modifier on an accessor in %s on line %d
