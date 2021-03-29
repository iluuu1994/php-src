--TEST--
Typealias cannot contain parent
--FILE--
<?php

typealias SelfAlias = parent;

?>
--EXPECTF--
Fatal error: parent cannot be used in typealiases in %s on line %d
