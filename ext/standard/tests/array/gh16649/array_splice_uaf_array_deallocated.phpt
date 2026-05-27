--TEST--
GH-16649: array_splice UAF when array is released entirely
--FILE--
<?php
class C {
    function __destruct() {
        global $arr;
        $arr = null;
    }
}

$arr = ["1", new C, "2"];

array_splice($arr, 1, 2);
?>
--EXPECT--

