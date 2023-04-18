--TEST--
Using parent::$prop::get() with undefined property
--FILE--
<?php

class P {}

class C extends P {
    public function test() {
        return parent::$prop::get();
    }
}

$c = new C();
try {
    var_dump($c->test());
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
Undefined property P::$prop
