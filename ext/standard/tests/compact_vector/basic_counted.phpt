--TEST--
CompactVector: basic counted type operations
--FILE--
<?php
$v = new CompactVector("counted");

$v[] = "hello";
$v[] = [1, 2, 3];
$v[] = new stdClass();
$v[] = null;

var_dump($v[0]);
var_dump($v[1]);
var_dump($v[2]);
var_dump($v[3]);

// Overwrite with different type
$v[0] = [4, 5];
var_dump($v[0]);
?>
--EXPECT--
string(5) "hello"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}
object(stdClass)#2 (0) {
}
NULL
array(2) {
  [0]=>
  int(4)
  [1]=>
  int(5)
}
