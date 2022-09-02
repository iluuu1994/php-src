--TEST--
GH-9434: ArrayObject should not bypass property type validation or magic __set
--FILE--
<?php

class C {
    public int $foo = 0;

    public function __set($name, $value) {
        var_dump($name, $value);
    }
}

$c = new C();
$a = new ArrayObject($c);
try {
    $a['foo'] = [];
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
try {
    $a['bar'] = 'bar';
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}
var_dump($c);

?>
--EXPECT--
Cannot assign array to property C::$foo of type int
string(3) "bar"
string(3) "bar"
object(C)#1 (1) {
  ["foo"]=>
  int(0)
}
