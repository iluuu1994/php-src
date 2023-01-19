--TEST--
Fake scope
--FILE--
<?php
class Foo {
    public string $bar;

    public function __construct() {
        unset($this->bar);
    }

    public function __get($name, $scope) {
        var_dump($scope);
        return $name;
    }

    public function __set($name, $value, $scope) {
        var_dump($scope);
    }
}

$foo = new Foo();
$reflectionProperty = new ReflectionProperty(Foo::class, 'bar');
$reflectionProperty->getValue($foo);
$reflectionProperty->setValue($foo, 'bar');

?>
--EXPECT--
string(3) "Foo"
string(3) "Foo"
