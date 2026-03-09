--TEST--
CompactVector: unset resets to zero
--FILE--
<?php
$v = new CompactVector("int32");
$v[] = 10;
$v[] = 20;
$v[] = 30;

unset($v[1]);
var_dump($v[1]); // should be 0 after unset

// Counted type: unset sets to null
$v = new CompactVector("counted");
$v[] = "hello";
$v[] = "world";

unset($v[0]);
var_dump($v[0]); // should be null after unset
var_dump($v[1]); // should still be "world"
?>
--EXPECT--
int(0)
NULL
string(5) "world"
