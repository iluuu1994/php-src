--TEST--
Using parent::$prop::get() in class with no parent
--FILE--
<?php

trait T {
    public $prop {
        get => parent::$prop::get();
    }
}

class Foo {
    use T;
}

$foo = new Foo();
try {
    var_dump($foo->prop);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
Cannot access "parent" when current class scope has no parent
