--TEST--
Basic tests for a complex type with an intersection type and union
--XFAIL--
This is not yet implemented
--FILE--
<?php

require_once 'array_likes.inc';

function foo(): ArrayAccess&Iterator|array {
    return new ArrayLike();
}

$o = foo();
var_dump($o);

?>
--EXPECT--
object(ArrayLike)#1 (0) {
}
