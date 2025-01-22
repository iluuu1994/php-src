--TEST--
Control flow in block
--FILE--
<?php

function test() {
    foreach ([1, 2, 3] as $v) {
        var_dump($v);
        str_repeat('a', 10) . (null ?? {
            continue;
        });
    }
}

test();

?>
--EXPECT--
int(1)
int(2)
int(3)
