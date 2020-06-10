--TEST--
ReflectionEnumUnitCase::getScalar()
--FILE--
<?php

enum Enum_ {
    case Foo;
}

enum IntEnum: int {
    case Foo = 0;
}

enum StringEnum: string {
    case Foo = 'Foo';
}

var_dump((new ReflectionEnumUnitCase(Enum_::class, 'Foo'))->getScalar());
var_dump((new ReflectionEnumUnitCase(IntEnum::class, 'Foo'))->getScalar());
var_dump((new ReflectionEnumUnitCase(StringEnum::class, 'Foo'))->getScalar());

?>
--EXPECT--
NULL
int(0)
string(3) "Foo"
