--TEST--
Test breaking out of match arm with continue 2
--FILE--
<?php

match (true) {
    default => {
        echo "$i 1\n";
        do {
            continue 2;
        } while (false);
        echo "$i 2\n";
    },
}

--EXPECTF--
Fatal error: "continue 2" targeting match is equivalent to "break 2". Did you mean to use "continue 3"? in %s on line %d
