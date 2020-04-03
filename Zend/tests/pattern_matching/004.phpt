--TEST--
Test object pattern binding
--FILE--
<?php

class Foo {
    public $bar;
    private $baz;

    public function __construct($bar, $baz) {
        $this->bar = $bar;
        $this->baz = $baz;
    }

    public function getBaz() {
        return $this->baz;
    }
}

class Bar {}

function wrong() {
    throw new Exception();
}

$foo = new Foo('bar', 'baz');

var_dump(match ($foo) {
    Bar {} => wrong(),
    Foo { bar: 'baz' } => wrong(),
    Foo { inexistentProp: 'nope' } => wrong(),
    Foo { bar: $bar @ 'bar', getBaz(): $baz } => 'Object pattern: ' . $bar . ' ' . $baz,
});

?>
--EXPECTF--

Warning: Undefined property: Foo::$inexistentProp in %s on line %d
string(23) "Object pattern: bar baz"
