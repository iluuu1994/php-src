--TEST--
Non-accessor property satisfies abstract property
--XFAIL--
Normal property should satisfy { get; set; }
--FILE--
<?php

abstract class A {
    abstract public $prop { get; set; }
}

class B extends A {
    public $prop;
}

?>
===DONE===
--EXPECT--
===DONE===
