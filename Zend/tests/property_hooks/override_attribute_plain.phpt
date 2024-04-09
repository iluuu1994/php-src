--TEST--
Override attribute is satisfied by plain property
--FILE--
<?php

class A {
    public $prop;
}

class B extends A {
    public $prop {
        #[Override]
        get => call_property_get_hook(parent::class, 'prop', $this);
    }
}

?>
===DONE===
--EXPECT--
===DONE===
