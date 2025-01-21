--TEST--
WIP
--FILE--
<?php

function test() {
    foreach ([1, 2, 3] as $value) {
        $x = match (1) {
            1 => { continue 2; },
        };
        var_dump($value);
    }
    return 42;
}

var_dump(test());

?>
--EXPECT--
int(42)
