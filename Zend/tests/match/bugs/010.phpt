--TEST--
WIP
--FILE--
<?php

var_dump(match (1) {
    1 => { break; }
});

?>
--EXPECTF--
Fatal error: break must not target match expression whose result is used in %s on line %d
