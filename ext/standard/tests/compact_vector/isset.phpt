--TEST--
CompactVector: isset/offsetExists behavior
--FILE--
<?php
$v = new CompactVector("int32");
$v[] = 0;
$v[] = 1;
$v[] = 0;

// isset returns true for all in-bounds slots (even 0 for non-counted)
var_dump(isset($v[0])); // true - 0 is valid
var_dump(isset($v[1])); // true
var_dump(isset($v[2])); // true - 0 is valid
var_dump(isset($v[3])); // false - out of bounds

// Counted: isset returns false for NULL slots
$v = new CompactVector("counted");
$v[] = "hello";
$v[] = null;

var_dump(isset($v[0])); // true
var_dump(isset($v[1])); // false - null
var_dump(isset($v[2])); // false - out of bounds

// empty() checks
$v = new CompactVector("int32");
$v[] = 0;
$v[] = 1;
var_dump(empty($v[0])); // true - 0 is empty
var_dump(empty($v[1])); // false - 1 is not empty
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(false)
bool(true)
bool(false)
bool(false)
bool(true)
bool(false)
