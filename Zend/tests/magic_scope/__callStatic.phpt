--TEST--
__callStatic() with scope
--FILE--
<?php

class Foo {
    public static function __callStatic($name, $args, $scope) {
        var_dump($scope);
    }

    public static function test() {
        self::bar();
        static::bar();
    }
}

class Child extends Foo {
    public static function __callStatic($name, $args, $scope) {
        var_dump($scope);
    }

    public static function test() {
        parent::test();
        self::bar();
        static::bar();
        parent::bar();
    }
}

Foo::bar();
Child::bar();

function test() {
    Foo::bar();
    Child::bar();
}
test();

Foo::test();
Child::test();

class Baz {}

$c = function () {
    Foo::bar();
    Child::bar();
};
$c->bindTo(null, Baz::class)();

?>
--EXPECT--
NULL
NULL
NULL
NULL
string(3) "Foo"
string(3) "Foo"
string(3) "Foo"
string(3) "Foo"
string(5) "Child"
string(5) "Child"
string(5) "Child"
string(3) "Baz"
string(3) "Baz"
