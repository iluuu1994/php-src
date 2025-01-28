--TEST--
WIP
--FILE--
<?php

function test() {
    foreach ([1, 2, 3] as $value) {
        str_repeat('a', 10) . match (1) {
            1 => {
                try {
                    return 42;
                } finally {}
            },
        };
        var_dump($value);
    }
    throw new Unreachable();
}

var_dump(test());

?>
--EXPECT--
int(42)
