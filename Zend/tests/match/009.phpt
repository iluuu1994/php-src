--TEST--
Pretty printing for match expression
--FILE--
<?php

assert((function () {
    match ('foo') {
        'foo', 'bar' => false,
        'baz' => false,
        default => {
            echo 'a';
            echo 'b';
            echo 'c';
        },
    };
})());

?>
--EXPECTF--
Warning: assert(): assert(function () {
    match ('foo') {
        'foo', 'bar' => false,
        'baz' => false,
        default => {
            echo 'a';
            echo 'b';
            echo 'c';
        },
    };
}()) failed in %s on line %d
