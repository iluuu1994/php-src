--TEST--
Pattern matching: Bug 001
--FILE--
<?php

function test($a) {
    $a is 42|43;
}

function test2($a) {
    $a === 42 || $a === 43;
}

var_dump(test(42));

?>
===DONE===
--EXPECT--
===DONE===
