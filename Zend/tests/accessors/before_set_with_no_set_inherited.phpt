--TEST--
Defining a get and beforeSet hook with no set hook via inheritance is illegal
--FILE--
<?php

class P {
    public $prop {
        get => 42;
    }
}

class C extends P {
    public $prop {
        beforeSet {}
    }
}

$c = new C();
$c->prop = 43;
var_dump($c);

?>
--EXPECTF--
Fatal error: Virtual read-only property C::$prop must not declare beforeSet hook in %s on line %d
