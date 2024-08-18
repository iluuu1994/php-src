--TEST--
Public class definition clashing with private class
--FILE--
<?php

private class C {}
class C {}

?>
--EXPECTF--
Fatal error: Cannot redeclare class C (previously declared as local import) in %s on line %d
