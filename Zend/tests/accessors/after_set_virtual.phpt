--TEST--
afterSet hook on virtual property is allowed
--FILE--
<?php

class C {
    private $_prop;
    public $prop {
        get {
            echo "get\n";
            return $this->_prop;
        }
        set {
            echo "set: $value\n";
            $this->_prop = $value;
        }
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
get
set: foo
afterSet: NULL
get
set: bar
afterSet: foo
