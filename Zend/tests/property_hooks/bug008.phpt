--TEST--
Assign by reference to backed property is allowed for &get-only
--FILE--
<?php

class Foo {
    private $_bar;
    public $bar {
        &get {
            echo __METHOD__, PHP_EOL;
            return $this->_bar;
        }
    }
}

$foo = new Foo;
$foo->bar = 'bar';
var_dump($foo);

?>
--EXPECTF--
Foo::$bar::get
object(Foo)#%d (1) {
  ["_bar":"Foo":private]=>
  string(3) "bar"
}
