--TEST--
Typealias in instanceof
--SKIPIF--
<?php die('skip does not work yet'); ?>
--FILE--
<?php

class Foo {}
class Bar extends Foo {}
class Baz {}

typealias FooAlias = Foo;

$foo = new Foo();
$bar = new Bar();
$baz = new Baz();

var_dump($foo instanceof FooAlias);
var_dump($bar instanceof FooAlias);
var_dump($baz instanceof FooAlias);

?>
--EXPECTF--
BOOL(true)
BOOL(true)
BOOL(false)
