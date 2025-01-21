--TEST--
WIP
--FILE--
<?php

function test() {
    return str_repeat('a', 10) . match (1) {
        1 => {
            try {
                try {
                    return 41;
                } finally {
                    throw new Exception();
                }
            } catch (Exception) {}
            'b'
        },
    };
}

var_dump(test());

?>
--EXPECT--
string(11) "aaaaaaaaaab"
