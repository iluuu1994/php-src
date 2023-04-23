--TEST--
beforeSet hook with typed parameter still doesn't allow wider values to be returned
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
            return $new;
        }
    }
}

$c = new C();

try {
    $c->prop = new A();
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
In beforeSet
Cannot assign A to property C::$prop of type B
