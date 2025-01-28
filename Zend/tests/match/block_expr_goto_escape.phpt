--TEST--
Match expression block must not use goto
--FILE--
<?php
var_dump(match (1) {
    1 => {
        goto after;
    },
});
after:
?>
===DONE===
--EXPECT--
===DONE===
