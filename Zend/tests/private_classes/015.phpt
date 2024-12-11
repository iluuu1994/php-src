--TEST--
Private function
--FILE--
<?php

private function test() {
    var_dump(__FUNCTION__);
}

test();

?>
--EXPECTF--
string(%d) "test@%s"
