--TEST--
Typealias cannot contain parent, even in union types
--FILE--
<?php

typealias SelfAlias = int|string|parent;

?>
--EXPECTF--
Fatal error: parent cannot be used in typealiases in %s on line %d
