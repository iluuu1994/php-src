--TEST--
Non-scalar enum cannot implement ScalarEnum
--FILE--
<?php

enum Foo implements ScalarEnum {}

?>
--EXPECTF--
Fatal error: Non-scalar enum Foo cannot implement interface ScalarEnum in %s on line %d
