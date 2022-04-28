--TEST--
Interaction of inaccessible accessors with magic methods
--FILE--
<?php

class A {
    public $privateGet {
        private get { echo __METHOD__, "\n"; }
        set { die('Unreachable'); }
    }

    public $privateSet {
        get { die('Unreachable'); }
        private set { echo __METHOD__, "\n"; }
    }
}

class B extends A {
    public function __get($name) {
        echo __METHOD__, "($name)\n";
        try {
            $this->$name;
        } catch (Error $e) {
            echo $e->getMessage(), "\n";
        }
    }
    public function __set($name, $value) {
        echo __METHOD__, "($name, $value)\n";
        try {
            $this->$name = $value;
        } catch (Error $e) {
            echo $e->getMessage(), "\n";
        }
    }
    public function __isset($name) {
        echo __METHOD__, "($name)\n";
        try {
            var_dump(isset($this->$name));
        } catch (Error $e) {
            echo $e->getMessage(), "\n";
        }
    }
    public function __unset($name) {
        echo __METHOD__, "($name)\n";
        try {
            unset($this->$name);
        } catch (Error $e) {
            echo $e->getMessage(), "\n";
        }
    }
}

$b = new B;
$b->privateGet;
isset($b->privateGet);
$b->privateSet = 1;
unset($b->privateSet);

?>
--EXPECT--
B::__get(privateGet)
Call to private accessor A::$privateGet::get() from scope B
B::__isset(privateGet)
bool(false)
B::__set(privateSet, 1)
Call to private accessor A::$privateSet::set() from scope B
B::__unset(privateSet)
Cannot unset accessor property B::$privateSet
