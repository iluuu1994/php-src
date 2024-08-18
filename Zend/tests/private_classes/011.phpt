--TEST--
Private trait
--FILE--
<?php

private trait T {
    public function test() {
        return __METHOD__;
    }
}

class C {
    use T;
}

var_dump(new C()->test());

?>
--EXPECTF--
string(77) "T@%s::test"
