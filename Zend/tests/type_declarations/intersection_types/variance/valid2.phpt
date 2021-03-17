--TEST--
Intersection with a child type is valid
--FILE--
<?php

interface A {}
interface B extends A{}
interface C {}

class Test implements A, B {}

class Foo {
    public function foo(): A&B {
        return new Test();
    }
}

class FooChild extends Foo {
    public function foo(): A&B {
        return new Test();
    }
}

class FooChildAgain extends Foo {
    public function foo(): B&A {
        return new Test();
    }
}

$o = new FooChild();
var_dump($o->foo());
$o = new FooChildAgain();
var_dump($o->foo());

?>
--EXPECTF--
object(Test)#%d (0) {
}
object(Test)#%d (0) {
}
