--TEST--
GH-16464: Use-after-free in SplDoublyLinkedList::offsetSet() when modifying list in destructor of overwritten object
--FILE--
<?php

class C {
    public $a;

    function __destruct() {
        global $list;
        var_dump($list->pop());
    }
}

$list = new SplDoublyLinkedList;
$list->add(0, new C);
$list[0] = 42;
(function() {})();
var_dump($list);

?>
--EXPECTF--
object(SplDoublyLinkedList)#%d (2) {
  ["flags":"SplDoublyLinkedList":private]=>
  int(0)
  ["dllist":"SplDoublyLinkedList":private]=>
  array(1) {
    [0]=>
    int(42)
  }
}
int(42)
