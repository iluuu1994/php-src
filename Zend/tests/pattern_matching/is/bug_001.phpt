--TEST--
Bug 001
--FILE--
<?php

function test() {
    $x = 42;
    43 is $x;
    var_dump($x);
}

test();

?>
--EXPECT--
int(43)
