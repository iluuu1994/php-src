--TEST--
WIP
--FILE--
<?php

class C {
    public function test($x) {}
}

function test() {
    (new C())->test(match (1) {
        1 => {
            return 42;
        }
    });
}

var_dump(test());

?>
--EXPECT--
int(42)
