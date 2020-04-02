--TEST--
Test identifier pattern binding
--FILE--
<?php

function wrong() {
    throw new Exception();
}

var_dump(match (24) {
    $a @ 0 ... 10 => wrong(),
    $a @ 10 ... 20 => wrong(),
    $a @ 20 ... 30 => 'Identifier pattern with range pattern: ' . $a,
    $a @ 30 ... 40 => wrong(),
    $a @ 40 ... 50 => wrong(),
});

?>
--EXPECT--
string(41) "Identifier pattern with range pattern: 24"
