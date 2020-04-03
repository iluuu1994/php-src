--TEST--
Test type check pattern binding
--FILE--
<?php

interface FooInterface {}
class Foo implements FooInterface {}
class Bar {}
class Baz {}

function wrong() {
    throw new Exception();
}

function test($value) {
    var_dump(match ($value) {
        is int|float => 'int|float',
        is ?string => '?string',
        is FooInterface|Bar => 'FooInterface|Bar',
        _ => '_',
    });
}

test(42);
test(3.141);
test(null);
test('foo');
test(new Foo());
test(new Bar());
test(new Baz());
test(false);

?>
--EXPECT--
string(9) "int|float"
string(9) "int|float"
string(7) "?string"
string(7) "?string"
string(16) "FooInterface|Bar"
string(16) "FooInterface|Bar"
string(1) "_"
string(1) "_"
