--TEST--
Recursion behavior of property hooks
--FILE--
<?php

class Test {
    public int $prop {
        get { return $this->prop * 2; }
        set { $this->prop = $value * 2; }
    }

    // Edge-case where recursion happens via isset().
    public int $prop2 {
        get { return isset($this->prop2); }
        set { }
    }
}

$test = new Test;
try {
    $test->prop = 10;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
try {
    var_dump($test->prop);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
try {
    var_dump(isset($test->prop));
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
try {
    var_dump(isset($test->prop2));
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
var_dump($test);

?>
--EXPECT--
Must not write to virtual property Test::$prop
Must not read from virtual property Test::$prop
Must not read from virtual property Test::$prop
Must not read from virtual property Test::$prop2
object(Test)#1 (0) {
}
