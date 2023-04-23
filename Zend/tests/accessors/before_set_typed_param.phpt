--TEST--
beforeSet hook with typed parameter allows a contravariant value to be assigned
--FILE--
<?php

declare(strict_types=1);

class C {
    public string $prop {
        beforeSet (int|string $new) {
            echo "beforeSet: $new\n";
            return (string) $new;
        }
    }
}

$c = new C();
$c->prop = 5;
var_dump($c->prop);
var_dump($c->prop);

?>
--EXPECT--
beforeSet: 5
string(1) "5"
string(1) "5"
