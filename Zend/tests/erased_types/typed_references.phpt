--TEST--
Erased types: Typed references
--FILE--
<?php

declare(types='erased');

class C {
    public string $prop;
}

$c = new C();
$c->prop = 'foo';
$ref = &$c->prop;
$ref = 42;
var_dump($c);

?>
--EXPECTF--
object(C)#%d (1) {
  ["prop"]=>
  &int(42)
}
