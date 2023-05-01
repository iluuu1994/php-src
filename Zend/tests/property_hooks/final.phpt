--TEST--
Final property hooks are not allowed
--FILE--
<?php

class A {
    public $prop {
        final get { return 42; }
    }
}

?>
--EXPECTF--
Fatal error: Cannot use the final modifier on a property hook in %s on line %d
