--TEST--
Array pattern
--FILE--
<?php

function test($a) {
    if (is_object($a) && $a->b === 42) {
        echo 'yes';
    }
}

function test2($a) {
    if ($a is { b: 42 }) {
        echo 'yes';
    }
}

?>
--EXPECT--
