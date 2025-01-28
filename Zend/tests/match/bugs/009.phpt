--TEST--
WIP
--FILE--
<?php

class C {
    public function test($x) {
        return $x;
    }
}

function test() {
    return (new C())->test(match (1) {
        1 => {
            try {
                throw new Exception();
            } catch (Exception) {}
            42
        }
    });
}

var_dump(test());

?>
--EXPECT--
int(42)
