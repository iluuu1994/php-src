--TEST--
Match blocks
--FILE--
<?php

class Foo {
    public function bar($value) {}
}

function foo() {
    return new Foo();
}

function test() {
    foo()?->bar(match (1) {
        1 { return 2; null }
    });
}

var_dump(test());

?>
--EXPECT--
int(2)
