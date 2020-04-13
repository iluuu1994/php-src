--TEST--
Test breaking out of match arm
--FILE--
<?php

foreach (range(0, 9) as $i) {
    match ($i) {
        default => {
            echo "$i 1\n";
            break;
            echo "$i 2\n";
        },
    }

    echo "$i 3\n";
}

--EXPECT--
0 1
0 3
1 1
1 3
2 1
2 3
3 1
3 3
4 1
4 3
5 1
5 3
6 1
6 3
7 1
7 3
8 1
8 3
9 1
9 3
