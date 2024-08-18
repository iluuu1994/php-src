--TEST--
Private classes must not be declared conditionally
--FILE--
<?php

if (mt_rand(0, 1) === 0) {
    private class C {}
} else {
    private class C {}
}

?>
--EXPECTF--
Fatal error: Private class C@%s must not be declared conditionally in %s on line %d
