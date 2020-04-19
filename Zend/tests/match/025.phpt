--TEST--
Test breaking out of loop inside match arm with continue 2
--FILE--
<?php

foreach (range(0, 9) as $i) {
    match ($i) {
        default => {
            echo "$i 1\n";
            continue 2;
            echo "$i 2\n";
        },
    }

    echo "$i 3\n";
}

--EXPECT--
0 1
1 1
2 1
3 1
4 1
5 1
6 1
7 1
8 1
9 1
