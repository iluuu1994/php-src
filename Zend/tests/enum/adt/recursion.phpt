--TEST--
ADTs may not contain recursive arrays
--FILE--
<?php

enum E {
    case A($x);
}

$x = [];
$x[] = &$x;
var_dump(E::A($x));

?>
--EXPECTF--
Fatal error: Uncaught Error: Nesting level too deep - recursive dependency? in %s:%d
%a
