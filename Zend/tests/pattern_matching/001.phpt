--TEST--
Test basic pattern matching
--FILE--
<?php

function wrong() {
    throw new Exception();
}

class Foo {
    const FOO = 'foo';
}

var_dump(match (true) {
    false => wrong(),
    true => 'Literal pattern with bool',
});

var_dump(match (4) {
    1 => wrong(),
    2 => wrong(),
    3 => wrong(),
    4 => 'Literal pattern with int',
    5 => wrong(),
    6 => wrong(),
});

var_dump(match ('e') {
    'a' => wrong(),
    'b' => wrong(),
    'c' => wrong(),
    'd' => wrong(),
    'e' => 'Literal pattern with string',
    'f' => wrong(),
    'g' => wrong(),
});

var_dump(match ('Foo') {
    1 => wrong(),
    2 => wrong(),
    3 => wrong(),
    $a => 'Identifier pattern: ' . $a,
    5 => wrong(),
    6 => wrong(),
});

var_dump(match ('Foo') {
    1 => wrong(),
    2 => wrong(),
    3 => wrong(),
    _ => 'Wildcard pattern',
});

var_dump(match (15) {
    0 ... 10 => wrong(),
    10 ... 20 => 'Range pattern',
    20 ... 30 => wrong(),
});

var_dump(match (true) {
    false if false => wrong(),
    false if true => wrong(),
    true if false => wrong(),
    true if true => 'Guard',
});

var_dump(match ('foo') {
    'bar' => wrong(),
    Foo::FOO => 'Class constant literal',
});

?>
--EXPECT--
string(25) "Literal pattern with bool"
string(24) "Literal pattern with int"
string(27) "Literal pattern with string"
string(23) "Identifier pattern: Foo"
string(16) "Wildcard pattern"
string(13) "Range pattern"
string(5) "Guard"
string(22) "Class constant literal"
