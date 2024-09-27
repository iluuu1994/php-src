--TEST--
Erased types: Uninitialized properties
--FILE--
<?php

declare(types='erased');

class C {
    public int $prop;
}

$c = new C();
var_dump($c);

try {
    $c->prop;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

try {
    $c->prop++;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECTF--
object(C)#%d (0) {
  ["prop"]=>
  uninitialized(int)
}
Typed property C::$prop must not be accessed before initialization
Typed property C::$prop must not be accessed before initialization
