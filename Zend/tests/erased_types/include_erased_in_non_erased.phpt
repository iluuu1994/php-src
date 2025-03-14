--TEST--
Erased types: Include erased in non-erased
--FILE--
<?php

require __DIR__ . '/include_erased_in_non_erased.inc';

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
int(42)
string(2) "p2"
string(2) "42"
int(43)
int(42)
int(42)
