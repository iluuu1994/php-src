--TEST--
Basic tests for intersection types
--FILE--
<?php

require_once 'array_likes.inc';

function foo(): ArrayAccess&Iterator {
    return new ArrayLike();
}

$o = foo();
var_dump($o);

?>
--EXPECT--
object(ArrayLike)#1 (0) {
}
