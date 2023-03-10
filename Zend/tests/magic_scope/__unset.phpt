--TEST--
__unset() with scope
--FILE--
<?php
class Foo {
    public function __unset($name) {
        var_dump(magic_method_get_calling_scope());
    }

    public function test() {
        unset($this->bar);
    }
}

$foo = new Foo();
unset($foo->bar);

function test() {
    $foo = new Foo();
    unset($foo->bar);
}
test();

$foo->test();

class Bar {
    public function test() {
        $foo = new Foo();
        unset($foo->bar);
    }
}
$bar = new Bar();
$bar->test();

class Baz {}

$c = function () {
    $foo = new Foo();
    unset($foo->bar);
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
string(3) "Foo"
string(3) "Bar"
string(3) "Baz"
