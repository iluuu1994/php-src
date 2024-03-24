--TEST--
Send data classes
--FILE--
<?php

data class Box {
    public function __construct(
        public $value,
    ) {}
}

$a = new Box(42);
$a->value = $a;
var_dump($a);

?>
--EXPECT--
object(Box)#2 (1) {
  ["value"]=>
  object(Box)#1 (1) {
    ["value"]=>
    int(42)
  }
}
