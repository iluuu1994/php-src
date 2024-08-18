--TEST--
Private classes doesn't clash with existing public class
--FILE--
<?php

require __DIR__ . '/013.inc';

// FIXME: Should we import private functions early?
var_dump(new C);

private class C {}

var_dump(new C);

?>
--EXPECTF--
object(C)#%d (0) {
}
object(C@%s)#1 (0) {
}
