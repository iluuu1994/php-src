--TEST--
Final accessors are not allowed
--FILE--
<?php

class A {
    public $prop {
        final get { return 42; }
    }
}

?>
--EXPECTF--
Fatal error: Cannot use the final modifier on an accessor in %s on line %d
