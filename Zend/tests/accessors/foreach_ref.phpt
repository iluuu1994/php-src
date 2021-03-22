--TEST--
Looping through object properties with accessors by ref errors
--FILE--
<?php

class Test {
    // foreach currently skips all UNDEF values, so this must have a default value
    public $a = 41 { get; set; }
}

$test = new Test;

try {
    foreach ($test as $prop => &$value) {
        $value = 42;
    }
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

var_dump($test);

?>
--EXPECT--
Cannot acquire reference to accessor property Test::$a
object(Test)#1 (1) {
  ["a"]=>
  int(41)
}
