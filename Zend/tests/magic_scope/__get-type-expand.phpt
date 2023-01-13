--TEST--
__get() with expanded scope type
--FILE--
<?php
class Foo {
    public function __get($name, string|bool|null $scope) {}
}
?>
--EXPECT--
