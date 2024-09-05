--TEST--
ADT constructor unwraps references
--FILE--
<?php

enum E {
    case A($x);
}

$y = 42;
$x = [&$y];
$e = E::A($x);
$y++;
var_dump($e);

?>
--EXPECTF--
Fatal error: Uncaught Error: Contains refs, todo in %s:%d
Stack trace:
#0 %s(%d): E::A(Array)
#1 {main}
  thrown in %s on line %d
