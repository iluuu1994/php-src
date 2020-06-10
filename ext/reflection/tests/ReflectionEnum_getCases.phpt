--TEST--
ReflectionEnum::getCases()
--FILE--
<?php

enum Foo {
    case Bar;
    case Baz;
    const Qux = self::Bar;
}

var_dump((new ReflectionEnum(Foo::class))->getCases());

?>
--EXPECT--
array(2) {
  [0]=>
  object(ReflectionEnumUnitCase)#2 (2) {
    ["name"]=>
    string(3) "Bar"
    ["class"]=>
    string(3) "Foo"
  }
  [1]=>
  object(ReflectionEnumUnitCase)#3 (2) {
    ["name"]=>
    string(3) "Baz"
    ["class"]=>
    string(3) "Foo"
  }
}
