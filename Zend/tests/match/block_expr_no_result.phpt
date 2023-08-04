--TEST--
Match expression block must return a value
--FILE--
<?php
var_dump(match (1) {
    1 => {
        echo "Not returning anything\n";
    },
});
?>
--EXPECTF--
Fatal error: Blocks of match expression with used result must return a value. Did you mean to omit the last semicolon? in %s on line %d
