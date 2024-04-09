--TEST--
Using call_property_get_hook(parent::class, 'prop', $this) on plain untyped uninitialized property
--FILE--
<?php

class P {
    public $prop;
}

class C extends P {
    public $prop {
        get => call_property_get_hook(parent::class, 'prop', $this);
    }
}

$c = new C();
var_dump($c->prop);

?>
--EXPECT--
NULL
