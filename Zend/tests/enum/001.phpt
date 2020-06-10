--TEST--
Basic enums
--FILE--
<?php

enum Foo {
    case Bar;
    case Baz(int $a);
    case Qux(int $a, string $b);

    public function dump() {
        var_dump($this);
    }
}

Foo::Bar()->dump();
Foo::Baz(10)->dump();
Foo::Qux(1, 'qux')->dump();

?>
--EXPECT--
object(Foo::Bar)#1 (0) {
}
object(Foo::Baz)#1 (1) {
  ["a"]=>
  int(10)
}
object(Foo::Qux)#1 (2) {
  ["a"]=>
  int(1)
  ["b"]=>
  string(3) "qux"
}
