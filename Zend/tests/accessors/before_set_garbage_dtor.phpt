--TEST--
Accessor beforeSet hook garbage dtor
--FILE--
<?php

class Boom {
    public function __destruct() {
        throw new Exception('Thrown from ' . __METHOD__);
    }
}

class C {
    public $prop = null {
        beforeSet {
            return 'foo';
        }
    }
}

$c = new C();
try {
    $c->prop = new Boom();
} catch (\Exception $e) {
    echo $e->getMessage(), "\n";
}
var_dump($c->prop);

?>
--EXPECT--
Thrown from Boom::__destruct
NULL
