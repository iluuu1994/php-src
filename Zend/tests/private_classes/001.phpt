--TEST--
Basic private class
--FILE--
<?php

private class Box {
    public function __construct(
        public int $value,
    ) {}
}

$box = new Box(42);
var_dump($box->value);
var_dump($box);
var_dump($box::class);
var_dump(Box::class);

?>
--EXPECTF--
int(42)
object(Box@%s)#1 (1) {
  ["value"]=>
  int(42)
}
string(%d) "Box@%s"
string(%d) "Box@%s"
