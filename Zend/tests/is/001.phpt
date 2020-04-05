--TEST--
Test is expression
--FILE--
<?php

namespace A {
    class Foo {}
    interface BarInterface {}
    class Bar implements BarInterface {}
}

namespace {
    function check_value($description, $value) {
        if ($value is int) print $description . ": int\n";
        if ($value is string) print $description . ": string\n";
        if ($value is float) print $description . ": float\n";
        if ($value is bool) print $description . ": bool\n";
        if ($value is array) print $description . ": array\n";
        if ($value is object) print $description . ": object\n";
        if ($value is A\Foo) print $description . ": A\Foo\n";
        if ($value is A\BarInterface) print $description . ": A\BarInterface\n";
        if ($value is ?int) print $description . ": ?int\n";
        if ($value is (string|float|null)) print $description . ": (string|float|null)\n";
        if ($value is (false|A\Foo|A\BarInterface)) print $description . ": (false|A\Foo|A\BarInterface)\n";
    }

    check_value("42", 42);
    check_value("'foo'", 'foo');
    check_value("3.141", 3.141);
    check_value("true", true);
    check_value("false", false);
    check_value("['foo', 'bar', 'baz']", ['foo', 'bar', 'baz']);
    check_value("(object) ['foo', 'bar', 'baz']", (object) ['foo', 'bar', 'baz']);
    check_value("fopen('php://output', 'r')", fopen('php://output', 'r'));
    check_value("null", null);
    check_value("function () {}", function () {});
    check_value("new A\Foo()", new A\Foo());
    check_value("new A\Bar()", new A\Bar());
}

?>
--EXPECT--
42: int
42: ?int
'foo': string
'foo': (string|float|null)
3.141: float
3.141: (string|float|null)
true: bool
false: bool
false: (false|A\Foo|A\BarInterface)
['foo', 'bar', 'baz']: array
(object) ['foo', 'bar', 'baz']: object
null: ?int
null: (string|float|null)
function () {}: object
new A\Foo(): object
new A\Foo(): A\Foo
new A\Foo(): (false|A\Foo|A\BarInterface)
new A\Bar(): object
new A\Bar(): A\BarInterface
new A\Bar(): (false|A\Foo|A\BarInterface)
