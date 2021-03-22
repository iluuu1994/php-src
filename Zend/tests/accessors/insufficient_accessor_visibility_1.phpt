--TEST--
Accessor doesn't match property visibility
--FILE--
<?php

// FIXME: This only gets triggered during inheritance
class Foo {}

class Test extends Foo {
    public $prop {
        private get { return 42; }
    }
}

?>
--EXPECTF--
Fatal error: At least one accessor must match the visibility of the property in %s on line %d
