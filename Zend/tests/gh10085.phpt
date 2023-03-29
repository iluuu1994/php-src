--TEST--
GH-10085: Assertion
--FILE--
<?php
$i = [[], 0];
$ref = &$i;
$i[0] += $ref;
?>
--EXPECT--
