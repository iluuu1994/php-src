--TEST--
ReflectionEnum::getCases()
--FILE--
<?php

enum Foo {
    case Bar;
    const Baz = self::Bar;
}

$reflectionEnum = new ReflectionEnum(Foo::class);

var_dump($reflectionEnum->getCase('Bar'));
var_dump($reflectionEnum->getCase('Baz'));
var_dump($reflectionEnum->getCase('Qux'));

?>
--EXPECT--
object(ReflectionEnumUnitCase)#2 (2) {
  ["name"]=>
  string(3) "Bar"
  ["class"]=>
  string(3) "Foo"
}
bool(false)
bool(false)
