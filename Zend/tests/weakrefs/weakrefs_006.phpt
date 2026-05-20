--TEST--
WeakReference overwriting existing value
--FILE--
<?php

class HasDtor {
    public function __destruct() {
        echo "In destruct\n";
        global $w, $all;
        for ($i = 0; $i < 10; $i++) {
            $v = new stdClass();
            $all[] = $v;
            $w[$v] = $i;
        }
    }
}
$all = [];
$w = new WeakMap();
$o = new stdClass();

$w[$o] = new HasDtor();
$w[$o] = 123;
(function () {})();
var_dump($w);
?>
--EXPECTF--
In destruct
object(WeakMap)#%d (11) {
  [0]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(123)
  }
  [1]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(0)
  }
  [2]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(1)
  }
  [3]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(2)
  }
  [4]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(3)
  }
  [5]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(4)
  }
  [6]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(5)
  }
  [7]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(6)
  }
  [8]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(7)
  }
  [9]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(8)
  }
  [10]=>
  array(2) {
    ["key"]=>
    object(stdClass)#%d (0) {
    }
    ["value"]=>
    int(9)
  }
}
