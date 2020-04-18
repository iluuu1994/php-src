--TEST--
Test match strict comparison with true expression
--FILE--
<?php

function wrong() {
    throw new Exception();
}

match (true) {
    'truthy' => wrong(),
    ['truthy'] => wrong(),
    new stdClass() => wrong(),
    1 => wrong(),
    1.0 => wrong(),
    true => { echo "true\n"; },
}

--EXPECT--
true
