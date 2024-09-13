--TEST--
Erased types: Parameter types
--FILE--
<?php

declare(types='erased');

function test(string $value) {
    var_dump($value);
}

test(42);

?>
--EXPECT--
int(42)
