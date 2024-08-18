--TEST--
Private enum
--FILE--
<?php

private enum E {
    case C;
}

class C {
    private E $e = E::C;

    public function test() {
        var_dump($this->e);
    }
}

new C()->test();

?>
--EXPECTF--
enum(E@%s::C)
