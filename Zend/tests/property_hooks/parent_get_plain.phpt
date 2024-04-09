--TEST--
Using call_property_get_hook(parent::class, 'prop', $this) on plain property
--FILE--
<?php

class P {
    public $prop = 42;
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
int(42)
