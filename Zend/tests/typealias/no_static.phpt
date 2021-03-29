--TEST--
Typealias cannot contain static
--FILE--
<?php

typealias StaticAlias = static;

?>
--EXPECTF--
Fatal error: static cannot be used in typealiases in %s on line %d
