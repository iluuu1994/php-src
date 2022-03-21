--TEST--
Allow arbitrary string interpolation expressions when interpolation starts with $
--FILE--
<?php

$foo = 1;

var_dump("{$foo + 41}");

?>
--EXPECT--
string(2) "42"
