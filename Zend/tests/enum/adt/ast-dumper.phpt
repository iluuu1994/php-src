--TEST--
ADT AST dumper
--FILE--
<?php

try {
    assert(false && (function () {
        enum Foo {
            case Bar($x, int $y);
        }
    }));
} catch (Error $e) {
    echo $e->getMessage();
}

?>
--EXPECT--
assert(false && function () {
    enum Foo {
        case Bar($x, int $y);
    }

})
