--TEST--
WIP
--FILE--
<?php

function test() {
    do {
        $foo = 'Hello';
        $bar = 'world';
        var_dump("$foo ${null ?? { 'bar' }}");
        var_dump("$foo ${null ?? { break; 'bar' }}");
    } while (0);
}

test();

?>
--EXPECTF--
Deprecated: Using ${expr} (variable variables) in strings is deprecated, use {${expr}} instead in %s on line %d

Deprecated: Using ${expr} (variable variables) in strings is deprecated, use {${expr}} instead in %s on line %d
string(11) "Hello world"
