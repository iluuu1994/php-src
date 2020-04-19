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
Fatal error: "continue" targeting match is equivalent to "break". Did you mean to use "continue 2"? in %s on line %d
