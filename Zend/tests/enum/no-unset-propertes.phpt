--TEST--
Enum properties cannot be unset
--FILE--
<?php

enum Foo {
    case Bar;
}

$foo = Foo::Bar;

try {
    unset($foo->case);
} catch (Error $e) {
    echo $e->getMessage() . "\n";
}

?>
--EXPECT--
Enum properties are immutable
