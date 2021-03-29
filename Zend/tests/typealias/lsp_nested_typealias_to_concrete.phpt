--TEST--
Nested typealias can be substituted with concrete types
--FILE--
<?php

class Foo {
    function __construct(private $string) {}
    function __toString() {
        return $this->string;
    }
}

typealias FooAlias = Foo;
typealias FooAliasAlias = FooAlias;

class Bar {
    function qux(): FooAliasAlias {
        return new Foo('From Bar');
    }
}

class Baz extends Bar {
    function qux(): Foo {
        return new Foo('From Baz');
    }
}

$baz = new Baz();
var_dump((string) $baz->qux());

?>
--EXPECT--
string(8) "From Baz"
