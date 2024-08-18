--TEST--
Private class definition clashing with public class
--FILE--
<?php

class C {}
private class C {}

?>
--EXPECTF--
Fatal error: Cannot use C@%s as C because the name is already in use in %s on line %d
