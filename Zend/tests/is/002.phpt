--TEST--
Pretty printing for is expression
--INI--
assert.exception=0
--FILE--
<?php

namespace Foo\Bar {
    class Baz {}
}

namespace {
    assert(false is int);
    assert(false is ?int);
    assert(false is \Foo\Bar\Baz);
    assert(false is (int|float|null));
}

?>
--EXPECTF--
Warning: assert(): assert(false is int) failed in %s on line %d

Warning: assert(): assert(false is ?int) failed in %s on line %d

Warning: assert(): assert(false is \Foo\Bar\Baz) failed in %s on line %d

Warning: assert(): assert(false is (int|float|null)) failed in %s on line %d
