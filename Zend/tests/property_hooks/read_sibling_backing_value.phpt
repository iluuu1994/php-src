--TEST--
Attempted read/write of backing value in sibling property hook fails
--FILE--
<?php

class Test {
    public $a = 1 {
        get => $this->a + $this->b;
    }
    public $b = 2 {
        get => $this->b + $this->a;
    }
}

$test = new Test;

try {
    var_dump($test->a);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
Must not access backing value of property Test::$a outside its corresponding hooks
