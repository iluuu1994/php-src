--TEST--
Assign by reference to backed property is allowed for &get-only
--FILE--
<?php

class Test {
    public $prop = 0 {
        &get {
            echo __METHOD__, "\n";
            return $this->prop;
        }
    }
}

$test = new Test();
$test->prop = &$ref;
$ref = 42;
var_dump($test);
$test->prop++;
var_dump($test);

?>
--EXPECTF--
object(Test)#%d (1) {
  ["prop"]=>
  &int(42)
}
Test::$prop::get
object(Test)#%d (1) {
  ["prop"]=>
  &int(43)
}
