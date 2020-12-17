--TEST--
is_enum
--SKIPIF--
<?php
die("skip, not yet implemented");
?>
--FILE--
<?php

enum Foo {
    case Bar;
}

class Baz {}

$enum_var = Foo::Bar;
$not_enum_var = 'narf';

// I'm not sure this one is correct?
var_dump(is_enum(Foo::class));

var_dump(is_enum(Foo::Bar::class));
var_dump(is_enum(Baz::class));

var_dump(is_enum('beep'));
var_dump(is_enum($enum_var));
var_dump(is_enum($not_enum_var));

?>
--EXPECT--
bool(true)
bool(true)
bool(false)
bool(false)
bool(true)
bool(false)
