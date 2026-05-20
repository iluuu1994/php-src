--TEST--
GH-10168: Assign ref
--FILE--
<?php

class Test {
    function __destruct() {
        $GLOBALS['a'] = null;
    }
}

$a = new Test;
$tmp = new Test;
var_dump($a = &$tmp);
(function () {})();

?>
--EXPECTF--
object(Test)#%d (0) {
}
