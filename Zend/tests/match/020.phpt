--TEST--
Test compilation error when using continue in match arm
--FILE--
<?php

foreach (range(0, 9) as $i) {
    match ($i) {
        default => {
            echo "$i 1\n";
            continue;
            echo "$i 2\n";
        },
    }

    echo "$i 3\n";
}

--EXPECTF--
Fatal error: "continue" targeting match is disallowed. Did you mean to use "break" or "continue 2"? in %s on line %d
