--TEST--
Override attribute is not satisfied by unilateral virtual property
--FILE--
<?php

class A {
    public $prop {
        set {}
    }
}

class B extends A {
    public $prop {
        #[Override]
        get => call_property_get_hook(parent::class, 'prop', $this);
    }
}

?>
--EXPECTF--
Fatal error: B::$prop::get() has #[\Override] attribute, but no matching parent method exists in %s on line %d
