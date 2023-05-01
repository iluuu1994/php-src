--TEST--
Dumping object with property hooks
--FILE--
<?php

class Test {
    public $prop = 1;
    public $prop2 {
        get { return 42; }
    }
}

class Child extends Test {
    public $prop1 {
        get { return 42; }
    }
}

function dump($test) {
  var_dump($test);
  var_dump(get_object_vars($test));
  var_dump((array) $test);
  foreach ($test as $prop => $value) {
      echo "$prop => $value\n";
  }
}

dump(new Test);
dump(new Child);

?>
--EXPECT--
object(Test)#1 (1) {
  ["prop"]=>
  int(1)
}
array(1) {
  ["prop"]=>
  int(1)
}
array(1) {
  ["prop"]=>
  int(1)
}
prop => 1
object(Child)#1 (1) {
  ["prop"]=>
  int(1)
}
array(1) {
  ["prop"]=>
  int(1)
}
array(1) {
  ["prop"]=>
  int(1)
}
prop => 1
