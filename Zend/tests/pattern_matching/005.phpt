--TEST--
Test type check pattern binding
--FILE--
<?php

interface FooInterface {}
class Foo implements FooInterface {}
class Bar {}
class Baz {}

function test($value) {
    var_dump(match ($value) {
        $v @ is int|float => var_export($v, true) . ': int|float',
        $v @ is ?string => var_export($v, true) . ': ?string',
        $v @ is FooInterface|Bar => get_class($v) . ': FooInterface|Bar',
        $v @ is object => get_class($v) . ': object',
        $v @ _ => var_export($v, true) . ': _',
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
string(13) "42: int|float"
string(16) "3.141: int|float"
string(13) "NULL: ?string"
string(14) "'foo': ?string"
string(21) "Foo: FooInterface|Bar"
string(21) "Bar: FooInterface|Bar"
string(11) "Baz: object"
string(8) "false: _"
