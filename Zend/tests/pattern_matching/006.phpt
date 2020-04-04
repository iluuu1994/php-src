--TEST--
Test error on unmatched pattern
--FILE--
<?php

function wrong() {
    throw new Exception();
}

var_dump(match ('foo') {
    is int => wrong(),
    is object => wrong(),
    is float => wrong(),
});

?>
--EXPECTF--
Fatal error: Uncaught InvalidArgumentException in %s
Stack trace:
#0 {main}
  thrown in %s on line %d
