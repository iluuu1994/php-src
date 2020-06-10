--TEST--
is_enum
--FILE--
<?php

enum Foo {
    case Bar;
}

class Baz {}

var_dump(is_enum(Foo::class));
var_dump(is_enum(Foo::Bar::class));
var_dump(is_enum(Baz::class));

?>
--EXPECT--
bool(true)
bool(true)
bool(false)
