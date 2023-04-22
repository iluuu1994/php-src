--TEST--
Accessor beforeSet hook on readonly property inherited
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
Fatal error: Virtual readonly property C::$prop must not declare beforeSet hook in %s on line %d
