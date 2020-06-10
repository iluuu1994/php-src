--TEST--
ReflectionEnum::getScalarType()
--FILE--
<?php

enum Enum_ {}
enum IntEnum: int {}
enum StringEnum: string {}

function test(): string {}

var_dump((new ReflectionEnum(Enum_::class))->getScalarType());
echo (new ReflectionEnum(IntEnum::class))->getScalarType() . "\n";
echo (new ReflectionEnum(StringEnum::class))->getScalarType() . "\n";

?>
--EXPECT--
NULL
int
string
