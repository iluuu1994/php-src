--TEST--
__isset() with scope
--FILE--
<?php
class Foo {
    public function __isset($name) {
        var_dump(magic_method_get_calling_scope());
        return true;
    }

    public function test() {
        isset($this->bar);
    }
}

$foo = new Foo();
isset($foo->bar);

function test() {
    $foo = new Foo();
    isset($foo->bar);
}
test();

$foo->test();

class Bar {
    public function test() {
        $foo = new Foo();
        isset($foo->bar);
    }
}
$bar = new Bar();
$bar->test();

class Baz {}

$c = function () {
    $foo = new Foo();
    isset($foo->bar);
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
string(3) "Foo"
string(3) "Bar"
string(3) "Baz"
