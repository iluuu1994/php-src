--TEST--
Or pattern
--FILE--
<?php

// var_dump(1 is 1);
// var_dump(1 is 2);
// var_dump(1 is 3);

// var_dump(1 is 2|3);
// var_dump(1 is 1|2|3);
// var_dump(1 is 2|1|3);

// var_dump(1 is 1&1);
// var_dump(1 is 1&2);
// var_dump(1 is 2&1);

// class Foo {}
// class Bar {}

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

class Foo {
    public $a = 1;
    public $b = 2;
    public $c = [1, 2, 3];
}

var_dump(null is {});
var_dump(new Foo is {});
var_dump(new Foo is { a: 1 });
var_dump(new Foo is { a: 2 });
var_dump(new Foo is { c: [...] });

?>
--EXPECT--
