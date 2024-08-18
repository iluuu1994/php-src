--TEST--
Rename global class with clashing private class
--FILE--
<?php

use C as CBase;

require __DIR__ . '/014.inc';

private class C extends CBase {
    public function test() {
        echo __METHOD__, "()\n";
        parent::test();
    }
}

new C()->test();

?>
--EXPECTF--
C@%s::test()
C::test()
