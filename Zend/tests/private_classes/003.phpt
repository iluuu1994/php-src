--TEST--
Private classes are not serializable
--FILE--
<?php

private class C {}

serialize(new C());

?>
--EXPECTF--
Fatal error: Uncaught Exception: Serialization of 'C@%s' is not allowed in %s:%d
%a
