--TEST--
GH-16649: array_splice UAF with destructor modifying array (original case)
--FILE--
<?php
function resize_arr() {
    global $arr;
    for ($i = 0; $i < 10; $i++) {
        $arr[$i] = $i;
    }
}

class C {
    function __destruct() {
        resize_arr();
        return "3";
    }
}

$arr = ["a" => "1", "3" => new C, "2" => "2"];

array_splice($arr, 1, 2);
?>
--EXPECT--
