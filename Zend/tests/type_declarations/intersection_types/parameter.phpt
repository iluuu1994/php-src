--TEST--
Intersection types in parameters
--FILE--
<?php

interface A {}
interface B {}

class Foo implements A, B {}
class Bar implements A {}

function foo(A&B $bar) {
    var_dump($bar);
}

foo(new Foo());
foo(new Bar());

?>
--EXPECT--
object(Foo)#1 (0) {
}
object(Bar)#1 (0) {
}
