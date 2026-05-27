--TEST--
GH-16649: array_splice UAF with complex array modification
--FILE--
<?php
class ComplexModifier {
    function __destruct() {
        global $arr;
        // complex modification that causes cow
        unset($arr[0]);
        $arr["new_key"] = "new_value";
        $arr[100] = "another_value";
    }
}

$arr = ["first", new ComplexModifier, "last"];

array_splice($arr, 1, 1, ["replacement"]);
?>
--EXPECT--
