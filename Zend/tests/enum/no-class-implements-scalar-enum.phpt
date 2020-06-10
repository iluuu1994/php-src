--TEST--
Class cannot implement ScalarEnum
--FILE--
<?php

class Foo implements ScalarEnum {}

?>
--EXPECTF--
Fatal error: Non-enum class Foo cannot implement interface ScalarEnum in %s on line %d
