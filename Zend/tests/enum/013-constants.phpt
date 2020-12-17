--TEST--
Enum allows constants
--SKIPIF--
<?php
die("skip, not yet implemented");
?>
--FILE--
<?php

enum Foo {
    const BAR = 'Bar';
}

print Foo::BAR;

?>
--EXPECTF--
Bar
