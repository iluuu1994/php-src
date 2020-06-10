--TEST--
enum_exists
--FILE--
<?php

enum Foo {
    case Bar;
}

class Baz {}

var_dump(enum_exists(Foo::class));
var_dump(enum_exists(Foo::Bar::class));
var_dump(enum_exists(Baz::class));
var_dump(enum_exists(Qux::class));

?>
--EXPECT--
bool(true)
bool(true)
bool(false)
bool(false)
