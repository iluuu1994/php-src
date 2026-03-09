--TEST--
CompactVector: clone creates independent copy
--FILE--
<?php
// Primitive type
$a = new CompactVector("int32");
$a[] = 1;
$a[] = 2;

$b = clone $a;
$b[0] = 99;

var_dump($a[0]); // original unchanged
var_dump($b[0]); // clone modified

// Counted type
$a = new CompactVector("counted");
$a[] = "hello";
$a[] = [1, 2, 3];

$b = clone $a;
$b[0] = "world";

var_dump($a[0]); // original unchanged
var_dump($b[0]); // clone modified
?>
--EXPECT--
int(1)
int(99)
string(5) "hello"
string(5) "world"
