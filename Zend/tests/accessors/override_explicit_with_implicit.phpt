--TEST--
Overriding explicit accessor with implicit accessor is illegal
--FILE--
<?php

class A {
    public $prop {
        get { die('Unreachable'); }
        set { die('Unreachable'); }
    }
}

class B extends A {
    public $prop {
        get;
        set;
    }
}

?>
--EXPECTF--
Fatal error: Implicit property accessor B::$prop::get() cannot override explicit property accessor A::$prop::get() in %s on line %d
