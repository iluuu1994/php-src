--TEST--
parent::$prop::set() ZPP
--FILE--
<?php

class A {
    public int $prop;
}

class B extends A {
    public int $prop {
        set {
            return parent::$prop::set($value, 42);
        }
    }
}

$b = new B();
try {
    $b->prop = 42;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECTF--
Fatal error: set() expects exactly 1 argument, 2 given in %s on line %d
