--TEST--
Erased types: Include non-erased in erased
--FILE--
<?php

declare(types='erased');

require __DIR__ . '/include_non_erased_in_erased.inc';

test(42);
test('42', 43);

class C extends P {
    use T;
}
$c = new C();
var_dump($c->test(42));
var_dump($c->test2(42));

?>
--EXPECT--
string(2) "42"
string(2) "p2"
string(2) "42"
string(2) "43"
string(2) "42"
string(2) "42"
