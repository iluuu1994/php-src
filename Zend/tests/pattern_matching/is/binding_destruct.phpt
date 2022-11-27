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
    42 is $foo or $bar;
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

// FIXME: This will change once bindings are delayed
var_dump($foo);
var_dump($bar);

?>
--EXPECT--
Here
int(42)
string(3) "bar"
