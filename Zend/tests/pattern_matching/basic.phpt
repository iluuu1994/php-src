--TEST--
Or pattern
--FILE--
<?php

class Foo {
    public $a = 1;
    public $b = 2;
    public $c = [1, 2, 3];

    const A = 1;
}
class Bar {}

// var_dump(1 is 1);
// var_dump(1 is 2);
// var_dump(1 is 3);

// var_dump(1 is 2|3);
// var_dump(1 is 1|2|3);
// var_dump(1 is 2|1|3);

// var_dump(1 is 1&1);
// var_dump(1 is 1&2);
// var_dump(1 is 2&1);

// var_dump(new Foo is Foo);
// var_dump(new Foo is Bar);
// var_dump(new Foo is ?Foo);
// var_dump(null is ?Foo);
// var_dump([1, 2, 3] is array);

// var_dump(null is []);
// var_dump([] is []);
// var_dump([1] is []);
// var_dump([1] is [...]);
// var_dump([1, 2] is [1, 2, 3]);
// var_dump([1, 2] is [2, 1]);
// var_dump([1, 2] is [1, 2, ...]);
// var_dump([1, 2, 3] is [1, 2, ...]);

// var_dump(null is {});
// var_dump(new Foo is {});
// var_dump(new Foo is { a: 1 });
// var_dump(new Foo is { a: 2 });
// var_dump(new Foo is { c: [...] });

// var_dump(1 is Foo::A);

// try {
//     var_dump(2 is Foo::B);
// } catch (Error $e) {
//     echo $e::class, ': ', $e->getMessage(), "\n";
// }

var_dump('Foo' is Foo::class);
var_dump('Bar' is Foo::class);

?>
--EXPECT--
