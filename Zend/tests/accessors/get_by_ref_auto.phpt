--TEST--
Get by reference with generated accessors
--FILE--
<?php

class Test {
    public $byVal = [] { get; set; }
}

$test = new Test;

try {
    $test->byVal[] = 42;
} catch (\Error $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
var_dump($test->byVal);

try {
    $test->byVal =& $ref;
} catch (Error $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}

?>
--EXPECT--
Error: Cannot aquire reference to accessor property Test::$byVal
array(0) {
}
Error: Cannot aquire reference to accessor property Test::$byVal
