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
    public $prop {
        get { return 42; }
    }
    public string $unsetTypedProp;
}

class ByRef {
    private $plainProp = 1;
    public $virtualByRefProp {
        &get { return $this->plainProp; }
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

$child = new Child;
$child->dynamicProp = 42;
dump($child);

$byRef = new ByRef();
foreach ($byRef as $prop => $value) {
    echo "$prop => $value\n";
    $value++;
}
var_dump($byRef);
foreach ($byRef as $prop => &$value) {
    echo "$prop => $value\n";
    $value++;
}
var_dump($byRef);

?>
--EXPECTF--
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
prop2 => 42

Deprecated: Creation of dynamic property Child::$dynamicProp is deprecated in %s on line %d
object(Child)#1 (2) {
  ["prop"]=>
  int(1)
  ["unsetTypedProp"]=>
  uninitialized(string)
  ["dynamicProp"]=>
  int(42)
}
array(2) {
  ["prop"]=>
  int(1)
  ["dynamicProp"]=>
  int(42)
}
array(2) {
  ["prop"]=>
  int(1)
  ["dynamicProp"]=>
  int(42)
}
prop => 42
prop2 => 42
dynamicProp => 42
virtualByRefProp => 1
object(ByRef)#2 (1) {
  ["plainProp":"ByRef":private]=>
  int(1)
}
virtualByRefProp => 1
object(ByRef)#2 (1) {
  ["plainProp":"ByRef":private]=>
  &int(2)
}
