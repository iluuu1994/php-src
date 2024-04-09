--TEST--
Calling hook method with call_property_get_hook(parent::class, 'prop', $this) from reflection
--FILE--
<?php

class A {
    public $prop;
}

class B extends A {
    public $prop {
        get {
            echo __FUNCTION__, "\n";
            return call_property_get_hook(parent::class, 'prop', $this);
        }
        set {
            echo __FUNCTION__, "\n";
            call_property_set_hook(parent::class, 'prop', $this, $value);
        }
    }
}

$b = new B();
(new ReflectionProperty(B::class, 'prop'))->getHook(PropertyHookType::Set)->invoke($b, 43);
var_dump((new ReflectionProperty(B::class, 'prop'))->getHook(PropertyHookType::Get)->invoke($b));

?>
--EXPECT--
$prop::set
$prop::get
int(43)
