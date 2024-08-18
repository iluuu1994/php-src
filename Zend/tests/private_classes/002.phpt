--TEST--
Private class inheritance
--FILE--
<?php

class P1 {
    public function test() {
        return __METHOD__ . "()\n";
    }
}
private class P2 {
    public function test() {
        return __METHOD__ . "()\n";
    }
}

private class C1 extends P1 {}
private class C2 extends P2 {}
class C3 extends P2 {}

echo new C1()->test();
echo new C2()->test();
echo new C3()->test();

?>
--EXPECTF--
P1::test()
P2@%s::test()
P2@%s::test()
