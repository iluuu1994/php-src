--TEST--
Test match as lhs of non statement expression
--FILE--
<?php

var_dump(match (null) { default => 10 } + 20);

?>
--EXPECT--
int(30)
