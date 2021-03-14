--TEST--
Basic tests for intersection types
--FILE--
<?php

require_once 'array_likes.inc';

function foo(): ArrayAccess&Iterator {
    return new ArrayIter();
}

try {
    $o = foo();
    var_dump($o);
} catch (\TypeError $e) {
    echo $e->getMessage(), "\n";
}

class A {
    //public ArrayAccess&Iterator $intersect;
}

$a = new A();
$o = new ArrayIter();
$a->intersect = $o;

?>
--EXPECT--
foo(): Return value must be of type ArrayAccess&Iterator, ArrayIter returned
