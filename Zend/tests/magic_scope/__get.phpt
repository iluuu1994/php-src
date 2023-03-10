--TEST--
__get() with scope
--FILE--
<?php
class Foo {
    public function __get($name) {
        var_dump(magic_method_get_calling_scope());
        return $name;
    }

    public function test() {
        $this->bar;
    }
}
class Child extends Foo {
    public function __get($name) {
        parent::__get($name);
    }

    public function testChild() {
        $this->bar;
    }
}

$foo = new Foo();
$foo->bar;

function test() {
    $foo = new Foo();
    $foo->bar;
}
test();

$foo->test();

$child = new Child();
$child->bar;
$child->test();
$child->testChild();

class Bar {
    public function test() {
        $foo = new Foo();
        $foo->bar;

        $child = new Child();
        $child->bar;
    }
}
$bar = new Bar();
$bar->test();

class Baz {}

$c = function () {
    $foo = new Foo();
    $foo->bar;
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
string(3) "Foo"
NULL
string(3) "Foo"
string(5) "Child"
string(3) "Bar"
string(3) "Bar"
string(3) "Baz"
