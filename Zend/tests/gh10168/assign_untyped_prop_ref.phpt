--TEST--
GH-10168: Assign prop ref
--FILE--
<?php

class Box {
    public $value;
}

class Test {
    function __destruct() {
        global $box;
        $box->value = null;
    }
}

function test($box) {
    $tmp = new Test;
    var_dump($box->value = &$tmp);
}

$box = new Box();
$box->value = new Test;
test($box);
// Second call tests the cache slot path
test($box);

?>
--EXPECTF--
object(Test)#%d (0) {
}
object(Test)#%d (0) {
}
