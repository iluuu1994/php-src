--TEST--
Overridden accessor property that adds get to set only property becomes invariant
--FILE--
<?php

class A {
    public int $prop {
        set { echo __CLASS__ . '::' . __METHOD__, "\n"; }
    }
}

class B extends A {
    public int|string $prop {
        get { echo __CLASS__ . '::' . __METHOD__, "\n"; return 42; }
        set { echo __CLASS__ . '::' . __METHOD__, "\n"; }
    }
}

?>
--EXPECTF--
Fatal error: Type of B::$prop must be int (as in class A) in %s on line %d
