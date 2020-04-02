--TEST--
Test array pattern
--FILE--
<?php

function wrong() {
    throw new Exception();
}

var_dump(match ([]) {
    0 => wrong(),
    null => wrong(),
    'foo' => wrong(),
    [1, 2, 3] => wrong(),
    [] => 'Empty array',
});

var_dump(match ([1, 2, 3]) {
    [] => wrong(),
    [1] => wrong(),
    [1, 2] => wrong(),
    [1, 2, 3] => 'Check array size',
});

var_dump(match ([1, 2, 3]) {
    [1 => 1, 2 => 2, 3 => 3] => wrong(),
    [0 => 1] => wrong(),
    [2 => 3] => wrong(),
    [2 => 3, 1 => 2, 0 => 1] => 'Explicit array keys',
});

var_dump(match ([1, 2, 3]) {
    [false, $a] => wrong(),
    [1, $a] => wrong(),
    [1, 2, $a] => 'Literal pattern in array pattern: ' . $a,
});

// FIXME: Memory leak with the wildcard pattern?
// var_dump(match (['foo', 24]) {
//     [_, 0 ... 10] => wrong(),
//     [_, 10 ... 20] => wrong(),
//     [_, 20 ... 30] => 'Range pattern in array pattern',
//     [_, 30 ... 40] => wrong(),
//     [_, 40 ... 50] => wrong(),
// });

?>
--EXPECT--
string(11) "Empty array"
string(16) "Check array size"
string(19) "Explicit array keys"
string(35) "Literal pattern in array pattern: 3"
