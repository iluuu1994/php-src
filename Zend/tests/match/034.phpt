--TEST--
Test compilation error when using break out of match with return value
--FILE--
<?php

var_dump(match(true) {
    default => {
        match(true) {
            default => {
                match(true) {
                    default => {
                        break;
                    }
                }
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
                        break 2;
                    }
                }
            }
        }
        2
    }
});

var_dump(match(true) {
    default => {
        match(true) {
            default => {
                match(true) {
                    default => {
                        break 3;
                    }
                }
            }
        }
        3
    }
});

--EXPECTF--
Fatal error: Breaking out of match with result value disallowed in %s034.php on line 39
