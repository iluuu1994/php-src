--TEST--
Basic tests for intersection types
--FILE--
<?php

interface A {}
interface B {}
interface C {}

class Test implements A, B, C {}

class Foo {
    public function foo(): A {
        return new Test;
    }
}

class Child extends Foo {
    public function foo(): A&B {
        return new Test();
    }
}

$o = new Child();
var_dump($o->foo());

?>
--EXPECT--
object(ArrayLike)#1 (0) {
}
