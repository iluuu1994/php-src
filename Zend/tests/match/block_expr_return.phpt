--TEST--
Match expression block must not use return
--FILE--
<?php

function test($a) {
    var_dump([$a] + match ($a) {
        42 => { return $a; },
    });
}

var_dump(test(42));

?>
--EXPECT--
int(42)
