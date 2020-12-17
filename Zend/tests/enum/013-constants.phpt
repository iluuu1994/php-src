--TEST--
Enum allows constants
--FILE--
<?php

enum Foo {
    const BAR = 'Bar';
}

print Foo::BAR;

?>
--EXPECTF--
Bar
