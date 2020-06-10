--TEST--
Enum properties cannot be written to
--FILE--
<?php

enum Foo {
    case Bar;
}

$bar = Foo::Bar;

try {
    $bar->case = 'Baz';
} catch (Error $e) {
    echo $e->getMessage() . "\n";
}

?>
--EXPECT--
Enum properties are immutable
