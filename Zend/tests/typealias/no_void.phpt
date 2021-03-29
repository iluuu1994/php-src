--TEST--
Typealias cannot contain void
--FILE--
<?php

typealias VoidAlias = void;

?>
--EXPECTF--
Fatal error: void cannot be used in typealiases in %s on line %d
