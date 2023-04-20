--TEST--
Property hooks in interfaces cannot be explicitly abstract
--FILE--
<?php

interface I {
    public $prop { abstract get; }
}

?>
--EXPECTF--
Fatal error: Property hook in interface cannot be explicitly abstract. All interface members are implicitly abstract in %s on line %d
