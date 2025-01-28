--TEST--
WIP
--FILE--
<?php

function test() {
    return str_repeat('a', 10) . match (1) {
        1 => {
            foreach (range(1, 10) as $value) {
                return 'foo';
            }
            'b'
        },
    };
}

var_dump(test());

?>
--EXPECT--
string(3) "foo"
