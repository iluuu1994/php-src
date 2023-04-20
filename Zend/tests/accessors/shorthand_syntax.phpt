--TEST--
Short-hand syntax for accessors
--FILE--
<?php

class Test {
    private $_prop;
    public $prop {
        get => $this->_prop;
        // The return value doesn't matter
        set => ($this->_prop = $value) + 1;
    }
}

$test = new Test();
var_dump($test->prop = 42);
var_dump($test->prop);

?>
--EXPECT--
int(42)
int(42)
