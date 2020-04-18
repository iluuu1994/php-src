--TEST--
Test match strict comparison with false expression
--FILE--
<?php

function wrong() {
    throw new Exception();
}

match (false) {
    '' => wrong(),
    [] => wrong(),
    0 => wrong(),
    0.0 => wrong(),
    false => { echo "false\n"; },
}

--EXPECT--
false
