--TEST--
Different kinds of indirect modification with by-val and by-ref getters
--FILE--
<?php


class Test {
    private $_byVal;
    public $byVal {
        get { return $this->_byVal; }
        set {
            echo __METHOD__, "\n";
            $this->_byVal = $value;
        }
    }
}

$test = new Test;

$test->byVal = 0;
$test->byVal++;
++$test->byVal;
$test->byVal += 1;
var_dump($test->byVal);
$test->byVal = [];
try {
    $test->byVal[] = 1;
} catch (\Error $e) {
    echo $e->getMessage(), "\n";
}
var_dump($test->byVal);
try {
    $ref =& $test->byVal;
} catch (\Error $e) {
    echo $e->getMessage(), "\n";
}
$ref = 42;
var_dump($test->byVal);

?>
--EXPECTF--
Test::$byVal::set
Test::$byVal::set
Test::$byVal::set
Test::$byVal::set
int(3)
Test::$byVal::set
Cannot aquire reference to accessor property Test::$byVal
array(0) {
}
Cannot aquire reference to accessor property Test::$byVal
array(0) {
}
