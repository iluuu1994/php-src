--TEST--
beforeSet allows specifying a custom parameter name
--FILE--
<?php

class C {
    public $prop {
        beforeSet ($new) {
            echo "beforeSet: $new\n";
            return strtoupper($new);
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
