--TEST--
WIP
--FILE--
<?php

function test() {
    return str_repeat('a', 10) . match (1) {
        1 => {
            try {
                try {
                    return str_repeat('a', 10) . match (1) {
                        1 => {
                            return 'foo';
                        },
                    };
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
