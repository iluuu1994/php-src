--TEST--
GH-16649: array_splice UAF when array is converted from packed to hash
--FILE--
<?php
class C {
    function __destruct() {
        global $arr;
        // array is converted from packed to hash
        $arr["str"] = 0;
    }
}

$arr = ["1", new C, "2"];

array_splice($arr, 1, 2);
?>
--EXPECT--
