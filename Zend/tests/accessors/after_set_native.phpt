--TEST--
afterSet hook on native property is allowed
--FILE--
<?php

class C {
    public $prop {
        afterSet {
            $tmp = $oldValue ?: 'NULL';
            echo "afterSet: $tmp\n";
        }
    }
}

$c = new C();
$c->prop = 'foo';
$c->prop = 'bar';

?>
--EXPECT--
afterSet: NULL
afterSet: foo
