--TEST--
Typed array as function parameter
--FILE--
<?php
function f1(array<int, string> $arr): void {}
function f2(array<int, array<int, string>> $arr): void {}
function f3(array<int, array<int, array<int, string>>> $arr): void {}
function f4(array<array<string>> $arr): void {}
function f5(array<array<array<string>>> $arr): void {}
?>
--EXPECT--
