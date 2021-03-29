--TEST--
Typealias cannot contain self
--FILE--
<?php

typealias SelfAlias = self;

?>
--EXPECTF--
Fatal error: self cannot be used in typealiases in %s on line %d
