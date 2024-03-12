--TEST--
Hooked properties with no default value are initialized to null
--FILE--
<?php

class Test {
    public $prop {
        get => $this->prop;
        set => $this->prop = $value;
    }
}

$test = new Test;
var_dump($test->prop);

?>
--EXPECT--
NULL
