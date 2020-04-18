--TEST--
Test jumping out of match arm using goto
--FILE--
<?php

match (true) {
    true => {
        goto after_match_expression;
        echo "Never executed\n";
    }
}

after_match_expression:
echo "After match expression";

--EXPECT--
After match expression
