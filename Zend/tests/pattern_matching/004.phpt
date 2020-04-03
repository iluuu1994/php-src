--TEST--
Test object pattern binding
--FILE--
<?php

class Foo {
    public $bar;
    public $baz;
}

class Bar {}

function wrong() {
    throw new Exception();
}

$foo = new Foo();
$foo->bar = 'bar';
$foo->baz = 'baz';

var_dump(match ($foo) {
    Bar {} => wrong(),
    Foo { bar: 'baz' } => wrong(),
    Foo { inexistentProp: 'nope' } => wrong(),
    Foo { bar: $bar @ 'bar' } => 'Object pattern: ' . $bar,
});

?>
--EXPECTF--

Warning: Undefined property: Foo::$inexistentProp in %s on line %d
string(19) "Object pattern: bar"
