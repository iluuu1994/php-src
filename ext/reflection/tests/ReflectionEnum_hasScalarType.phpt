--TEST--
ReflectionEnum::hasScalarType()
--FILE--
<?php

enum Enum_ {}
enum IntEnum: int {}
enum StringEnum: string {}

function test(): string {}

var_dump((new ReflectionEnum(Enum_::class))->hasScalarType());
var_dump((new ReflectionEnum(IntEnum::class))->hasScalarType());
var_dump((new ReflectionEnum(StringEnum::class))->hasScalarType());

?>
--EXPECT--
bool(false)
bool(true)
bool(true)
