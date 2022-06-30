--TEST--
Allow calling parent in accessors
--XFAIL--
Need syntax for accessing parent prop
--FILE--
<?php

class A {
    public int $prop {
        get {
            return 41;
        }
    }
}

class B extends A {
    public int $prop {
        get {
            return parent::$prop + 1;
        }
    }
}

$a = new A;
var_dump($a->prop);

$b = new B;
var_dump($b->prop);

?>
--EXPECT--
int(41)
int(42)
