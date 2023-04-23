--TEST--
beforeSet hook with typed parameter that is typed narrower than the property is legal but errors at runtime
--FILE--
<?php

class A {
    public function toB() {
        return new B();
    }
}

class B {}

class C {
    public A|B $prop {
        beforeSet (A $new) {
            return $new;
        }
    }
}

$c = new C();
$c->prop = new B();
var_dump($c->prop);

$c->prop = new A();
var_dump($c->prop);

?>
--EXPECTF--
Fatal error: Uncaught TypeError: C::$prop::beforeSet(): Argument #1 ($new) must be of type A, B given, called in %s:%d
Stack trace:
#0 %s(%d): C->$prop::beforeSet(Object(B))
#1 {main}
  thrown in %s on line %d
