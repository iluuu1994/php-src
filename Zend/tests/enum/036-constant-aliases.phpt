--TEST--
Enum constants can alias cases
--SKIPIF--
<?php
die("skip, not yet implemented");
?>
--FILE--
<?php

enum Foo {
    case Bar;
    const Other = self::Bar;
    const Again = static::Bar;
}

function test(Foo $var) {
    print "works";
}

test(Foo:Other);
test(Foo:Again);

?>
--EXPECTF--
works
works
