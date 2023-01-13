--TEST--
__get() with restricted scope type
--FILE--
<?php
class Foo {
    public function __get($name, string $scope) {}
}
?>
--EXPECTF--
Fatal error: Foo::__get(): Parameter #2 ($scope) must be of type ?string when declared in %s on line %d
