--TEST--
Enum __get
--FILE--
<?php

enum Foo {
    case Bar;

    public function __get(string $name): string
    {
        return '__get ' . $name . "\n";
    }
}

echo Foo::Bar->baz;
echo Foo::Bar->qux;

?>
--EXPECT--
__get baz
__get qux
