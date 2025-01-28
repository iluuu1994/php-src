--TEST--
WIP
--FILE--
<?php

function test() {
    str_repeat('a', 10) . match (1) {
        1 => {
            str_repeat('a', 10) . match (1) {
                1 => {
                    return 'foo';
                },
            };
        },
    };
}

var_dump(test());

?>
--EXPECT--
string(3) "foo"
