--TEST--
Enum is abstract
--SKIPIF--
<?php
die("skip doesn't work yet");
?>
--FILE--
<?php

enum Foo {}

try {
    new Foo();
} catch (\Error $e) {
    echo $e->getMessage();
}

?>
--EXPECT--
Cannot instantiate abstract class Foo
