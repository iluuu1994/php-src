--TEST--
Test compilation error trying to goto into a match expression
--FILE--
<?php

goto match_expression_false_arm;

match (true) {
    false => {
        match_expression_false_arm:
        echo 'Should not work';
    }
}

--EXPECTF--
Fatal error: 'goto' into loop, switch or match is disallowed in %s on line %d
