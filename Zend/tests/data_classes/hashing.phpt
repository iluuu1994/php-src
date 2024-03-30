--TEST--
Send data classes
--FILE--
<?php

data class Box {
    public $value;
}

$values = [
    null,
    false,
    0,
    1,
    42,
    -1,
    PHP_INT_MIN,
    PHP_INT_MAX,
    [],
    [1, 2, 3],
];

$map = new SplObjectStorage();

foreach ($values as $i => $value) {
    $map[new Box($value)] = $i;
}

foreach ($values as  $value) {
    var_dump($map[new Box($value)]);
}

?>
--EXPECTF--
Fatal error: Uncaught UnexpectedValueException: Object not found in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
