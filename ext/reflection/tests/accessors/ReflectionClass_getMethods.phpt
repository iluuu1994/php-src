--TEST--
ReflectionClass::getMethods() does not contain accessors
--FILE--
<?php

class Test {
    public $a { get; private set; }
}

var_dump((new ReflectionClass(Test::class))->getMethods());

?>
--EXPECT--
array(0) {
}
