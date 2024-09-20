--TEST--
Erased types: Parameter types
--FILE--
<?php

declare(types='erased');

function test(string $p1, string $p2 = 'p2') {
    var_dump($p1, $p2);
}

test(42, 43);

?>
--EXPECT--
int(42)
int(43)
