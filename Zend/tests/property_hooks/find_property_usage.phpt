--TEST--
Abstract hooks in non-abstract class gives an error
--FILE--
<?php

class Test {
    public $prop1 { get => $this->prop1; }
    public $prop2 { get => fn () => $this->prop2; }
    public $prop3 { get => function () { return $this->prop3; }; }
    public $prop4 { get => $this->prop1; }
    public $prop5 { get {} }
}

foreach ((new ReflectionClass(Test::class))->getProperties() as $prop) {
    var_dump($prop->isVirtual());
}

?>
--EXPECT--
bool(false)
bool(false)
bool(false)
bool(true)
bool(true)
