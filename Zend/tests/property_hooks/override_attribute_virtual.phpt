--TEST--
Override attribute is satisfied by virtual hook
--FILE--
<?php

class A {
    public $prop {
        get => 42;
    }
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
