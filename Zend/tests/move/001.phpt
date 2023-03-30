--TEST--
Basic move
--FILE--
<?php

function print_rc($value) {
    debug_zval_dump($value);
}

$array = [];
$array[] = 0;
print_rc($array);
print_rc(move $array);
var_dump(isset($array));

?>
--EXPECT--
array(1) refcount(3){
  [0]=>
  int(0)
}
array(1) refcount(2){
  [0]=>
  int(0)
}
bool(false)

