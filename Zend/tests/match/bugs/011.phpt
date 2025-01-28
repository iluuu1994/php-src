--TEST--
WIP
--FILE--
<?php

namespace Test;

function test() {
    $foo = 'Hello';
    $bar = 'world';
    var_dump("$foo ${null ?? { 'bar' }}");
    var_dump("$foo ${null ?? { return; 'bar' }}");
    str_repeat('a', 10) . (null ?? { return; 'bar' });
}

test();

?>
--EXPECTF--
Deprecated: Using ${expr} (variable variables) in strings is deprecated, use {${expr}} instead in %s on line %d

Deprecated: Using ${expr} (variable variables) in strings is deprecated, use {${expr}} instead in %s on line %d

Deprecated: Using ${expr} (variable variables) in strings is deprecated, use {${expr}} instead in %s on line %d
string(11) "Hello world"
