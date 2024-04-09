--TEST--
Override attribute is satisfied by backed property
--FILE--
<?php

class A {
    public $prop {
        set {
            $this->prop = 42;
        }
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
