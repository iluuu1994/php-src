--TEST--
Dumping object with accessors
--FILE--
<?php

class Test {
    public $prop = 1;
    public $prop2 {
        get { return 42; }
    }
}

$test = new Test;
var_dump($test);
var_dump(get_object_vars($test));
var_dump((array) $test);

foreach ($test as $prop => $value) {
    echo "$prop => $value\n";
}

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
