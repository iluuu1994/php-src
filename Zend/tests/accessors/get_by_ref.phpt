--TEST--
Get accessor by ref and indirect modification
--FILE--
<?php

class Test {
    public $_byVal = [];
    public $byVal {
        get { return $this->_byVal; }
        set { $this->_byVal = $value; }
    }
}

$test = new Test;

try {
    $test->byVal[] = 42;
} catch (\Error $e) {
    echo $e->getMessage(), "\n";
}
var_dump($test->byVal);

try {
    $test->byVal =& $ref;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
Cannot aquire reference to accessor property Test::$byVal
array(0) {
}
Cannot assign by reference to overloaded object
