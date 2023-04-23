--TEST--
beforeSet hook inheritance can access parent hooks
--FILE--
<?php

class P {
    public $prop {
        beforeSet {
            echo __CLASS__, "\n";
            return $value;
        }
    }
    public $prop2;
}

class C extends P {
    public $prop {
        beforeSet {
            echo __CLASS__, "\n";
            return parent::$prop::beforeSet($value);
        }
    }
    public $prop2 = null {
        beforeSet {
            echo __CLASS__, "\n";
            return parent::$prop2::beforeSet($value);
        }
    }
}

$c = new C();

$c->prop = 'foo';
echo $c->prop, "\n";
echo $c->prop, "\n";

try {
    $c->prop2 = 'foo';
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
var_dump($c->prop);

?>
--EXPECT--
C
P
foo
foo
C
Call to undefined method P::$prop2::beforeSet()
string(3) "foo"
