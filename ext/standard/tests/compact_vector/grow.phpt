--TEST--
CompactVector: dynamic growth and sparse access
--FILE--
<?php
$v = new CompactVector("int32");

// Append
$v[] = 42;
$v[] = -1;

// Sparse write - gap should be zero-filled
$v[100] = 7;

var_dump($v[0]);   // 42
var_dump($v[1]);   // -1
var_dump($v[50]);  // 0 (zero-filled)
var_dump($v[99]);  // 0 (zero-filled)
var_dump($v[100]); // 7
?>
--EXPECT--
int(42)
int(-1)
int(0)
int(0)
int(7)
