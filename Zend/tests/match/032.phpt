--TEST--
Test match block return value
--FILE--
<?php

function test($value) {
    return match ($value) {
        1 => {
            echo '1';
            echo '2';
            '3'
        },
        2 => {
            echo '4';
            echo '5';
            '6'
        },
        3 => {
            echo '7';
            echo '8';
            '9'
        },
    };
}

print test(1) . "\n";
print test(2) . "\n";
print test(3) . "\n";

--EXPECT--
123
456
789
