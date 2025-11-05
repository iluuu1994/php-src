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

class Foo {}
class Bar {}

var_dump(new Foo is Foo);
var_dump(new Foo is Bar);
var_dump(new Foo is ?Foo);
var_dump(null is ?Foo);

?>
--EXPECT--
