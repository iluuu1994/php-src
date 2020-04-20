--TEST--
Test match block warning with unnecessary return value
--FILE--
<?php

function test($value) {
    match ($value) {
        1 => {
            echo '1';
            echo '2';
            '3';
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
    }
}

test(1);
test(2);
test(3);

--EXPECTF--
Warning: Useless expression. Did you mean to add a semicolon? in %s033.php on line 12

Warning: Useless expression. Did you mean to add a semicolon? in %s033.php on line 17
124578
