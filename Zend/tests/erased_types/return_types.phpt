--TEST--
Erased types: Return types
--FILE--
<?php

declare(types='erased');

function test(): string {
    return 42;
}

var_dump(test());

?>
--EXPECT--
int(42)
