--TEST--
Test compilation error when using goto to jump out of match with return value 2
--FILE--
<?php

function test1(bool $flag) {
    $x = [new stdClass(), match(true) {
        default => {
            if ($flag) {
                goto outer;
            }
            null
        }
    }];
    outer:
    echo "end of loop\n";
}

test1(true);

--EXPECTF--
Fatal error: 'goto' out of match with a return value is disallowed in %s036.php on line 7
