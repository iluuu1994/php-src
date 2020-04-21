--TEST--
Test compilation error when using goto to jump out of match with return value
--FILE--
<?php

var_dump(match(true) {
    default => {
        match(true) {
            default => {
                match(true) {
                    default => {
                        goto after_match_1;
                    }
                }
                after_match_1:
            }
        }
        1
    }
});

var_dump(match(true) {
    default => {
        match(true) {
            default => {
                match(true) {
                    default => {
                        goto after_match_2;
                    }
                }
            }
        }
        after_match_2:
        2
    }
});

var_dump(match(true) {
    default => {
        match(true) {
            default => {
                match(true) {
                    default => {
                        goto after_match_3;
                    }
                }
            }
        }
        3
    }
});
after_match_3:

--EXPECTF--
Fatal error: 'goto' out of match with a return value is disallowed in %s035.php on line 41
