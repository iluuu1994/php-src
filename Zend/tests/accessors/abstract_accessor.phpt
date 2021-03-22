--TEST--
Abstract accessors
--FILE--
<?php

abstract class A {
    public $prop {
        abstract get;
        set { echo __METHOD__, "()\n"; }
    }
}

class B extends A {
    public $prop {
        get { echo __METHOD__, "()\n"; return 42; }
    }
}

?>
===DONE===
--EXPECT--
===DONE===
