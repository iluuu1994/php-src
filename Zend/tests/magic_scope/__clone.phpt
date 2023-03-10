--TEST--
__clone() with scope
--FILE--
<?php
class Foo {
    public function __clone() {
        var_dump(magic_method_get_calling_scope());
    }

    public function test() {
        clone $this;
    }
}

$foo = new Foo();
clone $foo;

function test() {
    $foo = new Foo();
    clone $foo;
}
test();

$foo->test();

class Bar {
    public function test() {
        $foo = new Foo();
        clone $foo;
    }
}
$bar = new Bar();
$bar->test();

class Baz {}

$c = function () {
    $foo = new Foo();
    clone $foo;
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
string(3) "Foo"
string(3) "Bar"
string(3) "Baz"
