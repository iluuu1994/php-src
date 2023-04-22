--TEST--
Accessor beforeSet hook exception
--FILE--
<?php

class C {
    public $prop = null {
        beforeSet {
            echo "beforeSet: $value\n";
            throw new \Exception('Throwing from beforeSet');
        }
    }
}

$c = new C();
try {
    $c->prop = 'foo';
} catch (\Exception $e) {
    echo $e->getMessage(), "\n";
}
var_dump($c->prop);

?>
--EXPECT--
beforeSet: foo
Throwing from beforeSet
NULL
