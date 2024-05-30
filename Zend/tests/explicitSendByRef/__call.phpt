--TEST--
__call() magic with explicit pass by ref
--FILE--
<?php

class Incrementor {
    public function inc(&$i) {
        $i++;
    }
}

class ForwardCalls {
    private $object;
    public function __construct($object) {
        $this->object = $object;
    }
    public function __call(string $method, array $args) {
        return $this->object->$method(...$args);
    }
}

$forward = new ForwardCalls(new Incrementor);

$i = 0;
try {
    $forward->inc(&$i);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
var_dump($i);

$i = 0;
$forward->inc($i);
var_dump($i);

?>
--EXPECT--
Cannot pass reference to by-value parameter 1
int(0)
int(0)
