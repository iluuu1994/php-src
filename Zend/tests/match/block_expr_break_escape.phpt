--TEST--
Match expression block must not use break
--FILE--
<?php
function test() {
    str_repeat('a', 10) . match (1) {
        1 => { return; },
    };
}

test();
?>
===DONE===
--EXPECT--
===DONE===
