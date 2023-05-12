--TEST--
Fake scope
--FILE--
<?php
class Foo {
    public string $bar;

    public function __construct() {
        unset($this->bar);
    }

    public function __get($name) {
        var_dump(magic_method_get_calling_scope());
        return $name;
    }

    public function __set($name, $value) {
        var_dump(magic_method_get_calling_scope());
    }
}

class Bar extends Foo {}

class CustomReflectionProperty extends \ReflectionProperty {
    public static $callParent = false;

    public function getValue(?object $object = null): mixed {
        if (self::$callParent) {
            return parent::getValue($object);
        } else {
            return $object->{$this->getName()};
        }
    }
    public function setValue(mixed $objectOrValue, mixed $value = null): void {
        if (self::$callParent) {
            parent::setValue($objectOrValue, $value);
        } else {
            $objectOrValue->{$this->getName()} = $value;
        }
    }
}

$foo = new Foo();
$reflectionProperty = new ReflectionProperty(Foo::class, 'bar');
$reflectionProperty->getValue($foo);
$reflectionProperty->setValue($foo, 'bar');

$foo = new Foo();
$reflectionProperty = new CustomReflectionProperty(Foo::class, 'bar');
$reflectionProperty->getValue($foo);
$reflectionProperty->setValue($foo, 'bar');

CustomReflectionProperty::$callParent = true;
$foo = new Foo();
$reflectionProperty = new CustomReflectionProperty(Foo::class, 'bar');
$reflectionProperty->getValue($foo);
$reflectionProperty->setValue($foo, 'bar');

$bar = new Bar();
$reflectionProperty = new ReflectionProperty(Foo::class, 'bar');
$reflectionProperty->getValue($bar);
$reflectionProperty->setValue($bar, 'bar');

?>
--EXPECT--
string(3) "Foo"
string(3) "Foo"
string(24) "CustomReflectionProperty"
string(24) "CustomReflectionProperty"
string(3) "Foo"
string(3) "Foo"
string(3) "Foo"
string(3) "Foo"
