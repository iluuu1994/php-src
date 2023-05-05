--TEST--
Accessing property from hook does not call magic method
--FILE--
<?php

class Test {
    public $prop {
        get => $this->prop;
        set => $this->prop = $value;
    }

    public function __get($name) {
        echo __METHOD__, "\n";
        return 42;
    }

    public function __set($name, $value) {
        echo __METHOD__, "\n";
    }
}

$test = new Test;
$test->prop;
$test->prop = 42;

?>
--EXPECTF--
Warning: Undefined property: Test::$prop in %s on line %d
