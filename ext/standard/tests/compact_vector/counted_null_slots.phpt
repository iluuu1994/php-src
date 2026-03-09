--TEST--
CompactVector: counted type unwritten slots return null
--FILE--
<?php
$v = new CompactVector("counted");

// Sparse write - gap should be null
$v[5] = "hello";

var_dump($v[0]); // null - unwritten
var_dump($v[3]); // null - unwritten
var_dump($v[5]); // "hello"

// isset returns false for null slots
var_dump(isset($v[0])); // false
var_dump(isset($v[5])); // true
?>
--EXPECT--
NULL
NULL
string(5) "hello"
bool(false)
bool(true)
