--TEST--
Final property is not allowed
--FILE--
<?php

class A {
    public final $prop { get {} set {} }
}

?>
--EXPECTF--
Fatal error: Cannot use the final modifier on a property in %s on line %d
