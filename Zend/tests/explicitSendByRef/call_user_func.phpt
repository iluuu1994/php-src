--TEST--
call_user_func() with explicit pass by ref
--FILE--
<?php

function inc(&$i) { $i++; }

$i = 0;
call_user_func('Foo\inc', &$i);
var_dump($i);

?>
--EXPECTF--
Fatal error: Cannot pass reference to by-value parameter 2 in %s on line %d
