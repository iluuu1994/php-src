--TEST--
beforeSet hook with typed parameter allows a contravariant value to be assigned (class version)
--FILE--
<?php

class A {
    public function toB() {
        return new B();
    }
}

class B {}

class C {
    public B $prop {
        beforeSet (A|B $new) {
            echo "In beforeSet\n";
            if ($new instanceof A) {
                $new = $new->toB();
            }
            return $new;
        }
    }
}

$c = new C();
$c->prop = new A();
var_dump($c->prop);
var_dump($c->prop);

?>
--EXPECT--
In beforeSet
object(B)#3 (0) {
}
object(B)#3 (0) {
}
