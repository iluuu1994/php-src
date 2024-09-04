--TEST--
ADTs in match error message
--FILE--
<?php

enum E {
    case A($x);
}

match (E::A(42)) {};

?>
--EXPECTF--
Fatal error: Uncaught UnhandledMatchError: Unhandled match case E::A in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
