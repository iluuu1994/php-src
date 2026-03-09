--TEST--
CompactVector: basic bool operations
--FILE--
<?php
$v = new CompactVector("bool");

$v[] = true;
$v[] = false;
$v[] = true;

var_dump($v[0], $v[1], $v[2]);

// Overwrite
$v[1] = true;
var_dump($v[1]);
?>
--EXPECT--
bool(true)
bool(false)
bool(true)
bool(true)
