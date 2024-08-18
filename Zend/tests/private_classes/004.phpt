--TEST--
Duplicate private class definitions
--FILE--
<?php

private class C {}
private class C {}

?>
--EXPECTF--
Fatal error: Cannot use class C@%s as C because the name is already in use in %s on line %d
