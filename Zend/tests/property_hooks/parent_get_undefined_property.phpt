--TEST--
Using parent::$prop::get() with undefined property
--FILE--
<?php

class P {}

class C extends P {
    public $prop {
        get {
            return parent::$prop::get();
        }
    }
}

$c = new C();
try {
    var_dump($c->prop);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
Call to undefined method P::$prop::get()
