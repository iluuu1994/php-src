--TEST--
Accessor beforeSet hook
--FILE--
<?php

class C {
    public $prop { 
        beforeSet {
            echo "beforeSet: $value\n";
            return strtoupper($value);
        }
    }
}

$c = new C();
$c->prop = 'foo';
echo $c->prop, "\n";
echo $c->prop, "\n";

?>
--EXPECT--
beforeSet: foo
FOO
FOO
