--TEST--
Abstract hooks compile successfully
--FILE--
<?php

abstract class A {
    public $prop {
        abstract get;
        set {}
    }
}

class B extends A {
    public $prop {
        get {}
    }
}

?>
===DONE===
--EXPECT--
===DONE===
