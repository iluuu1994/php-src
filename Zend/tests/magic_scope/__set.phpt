--TEST--
__set() with scope
--FILE--
<?php
class Foo {
    public function __set($name, $value, $scope) {
        var_dump($scope);
    }

    public function test() {
        $this->bar = 'bar';
    }
}

$foo = new Foo();
$foo->bar = 'bar';

function test() {
    $foo = new Foo();
    $foo->bar = 'bar';
}
test();

$foo->test();

class Bar {
    public function test() {
        $foo = new Foo();
        $foo->bar = 'bar';
    }
}
$bar = new Bar();
$bar->test();

class Baz {}

$c = function () {
    $foo = new Foo();
    $foo->bar = 'bar';
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
string(3) "Foo"
string(3) "Bar"
string(3) "Baz"
