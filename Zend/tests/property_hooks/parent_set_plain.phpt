--TEST--
Using call_property_set_hook(parent::class, 'prop', $this, ) on plain property
--FILE--
<?php

class P {
    public $prop;
}

class C extends P {
    public $prop {
        set {
            var_dump(call_property_set_hook(parent::class, 'prop', $this, $value));
        }
    }
}

$c = new C();
$c->prop = 42;

?>
--EXPECT--
int(42)
