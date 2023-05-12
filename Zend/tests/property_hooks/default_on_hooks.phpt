--TEST--
Backed property may have default value
--FILE--
<?php

class A {
    private $prop { get {} set {} }
}

class B extends A {
    public $prop = 42 {
        get {
            echo __METHOD__, "\n";
            return field;
        }
        set {
            echo __METHOD__, "\n";
            field = $value;
        }
    }
}

$b = new B();
var_dump($b);
var_dump($b->prop);
$b->prop = 43;
var_dump($b->prop);

?>
--EXPECT--
object(B)#1 (1) {
  ["prop"]=>
  int(42)
}
B::$prop::get
int(42)
B::$prop::set
B::$prop::get
int(43)
