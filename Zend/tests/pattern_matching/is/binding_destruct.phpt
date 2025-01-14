--TEST--
Object pattern matching destructor
--FILE--
<?php

class Foo {
    public function __destruct() {
        throw new Exception('Here');
    }
}

$foo = new Foo();
$bar = 'bar';

try {
    42 is $foo & $bar;
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

var_dump($foo);
var_dump($bar);

?>
--EXPECT--
Here
int(42)
int(42)
