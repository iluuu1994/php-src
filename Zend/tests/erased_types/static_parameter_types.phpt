--TEST--
Erased types: Static property types
--FILE--
<?php

declare(types='erased');

class C {
    public static string $prop;
}

C::$prop = 42;
var_dump(C::$prop);

?>
--EXPECT--
int(42)
