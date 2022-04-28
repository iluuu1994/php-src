--TEST--
Overridden accessor property that adds set to get only property becomes invariant
--FILE--
<?php

class A {
    public int|string $prop {
        get { echo __CLASS__ . '::' . __METHOD__, "\n"; return 42; }
    }
}

class B extends A {
    public int $prop {
        get { echo __CLASS__ . '::' . __METHOD__, "\n"; return 42; }
        set { echo __CLASS__ . '::' . __METHOD__, "\n"; }
    }
}

?>
--EXPECTF--
Fatal error: Type of B::$prop must be string|int (as in class A) in %s on line %d
