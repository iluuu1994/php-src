--TEST--
__call() with scope
--FILE--
<?php
class Foo {
    public function __call($name, $args, $scope) {
        var_dump($scope);
    }

    public function test() {
        $this->bar();
    }
}

$foo = new Foo();
$foo->bar();

function test() {
    $foo = new Foo();
    $foo->bar();
}
test();

$foo->test();

class Bar {
    public function test() {
        $foo = new Foo();
        $foo->bar();
    }
}
$bar = new Bar();
$bar->test();

class Baz {}

$c = function () {
    $foo = new Foo();
    $foo->bar();
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
string(3) "Foo"
string(3) "Bar"
string(3) "Baz"
