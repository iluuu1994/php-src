--TEST--
beforeSet hook on virtual property is allowed
--FILE--
<?php

class C {
    public $prop {
        beforeSet {
            echo "beforeSet: $value\n";
            return strtoupper($value);
        }
        set {
            echo "set: $value\n";
        }
    }
}

$c = new C();
$c->prop = 'foo';

?>
--EXPECT--
beforeSet: foo
set: FOO
