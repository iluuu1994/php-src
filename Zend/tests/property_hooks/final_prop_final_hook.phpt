--TEST--
Cannot make hook explicitly final in final property
--FILE--
<?php

class Test {
    final public $prop {
        final get;
        final set;
    }
}

?>
--EXPECTF--
Fatal error: Hook on final property cannot be explicitly final in %s on line %d
