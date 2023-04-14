--TEST--
ReflectionClass::getMethods() does not contain accessors
--FILE--
<?php

class Test {
    public $a { get {} set {} }
}

var_dump((new ReflectionClass(Test::class))->getMethods());

?>
--EXPECT--
array(0) {
}
