--TEST--
Match expression block must not use return
--FILE--
<?php
function test() {
    var_dump(match (1) {
        1 => { return; }
    });
    echo 'Unreached';
}
test();
?>
===DONE===
--EXPECT--
===DONE===
