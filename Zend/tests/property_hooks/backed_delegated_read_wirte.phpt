--TEST--
Attempted read/write of backing value in a delegated method throws
--FILE--
<?php

class Test {
    public $prop = 42 {
        get => $this->getProp($this->prop);
        set {
            $this->setProp($this->prop, $value);
        }
    }

    private function getProp($prop) {
        return $this->prop;
    }

    private function setProp($prop, $value) {
        $this->prop = $value;
    }

    public $prop2 = 42 {
        get => $this->prop2;
        set => $value;
    }
}

class Child extends Test {
    public $prop2 = 42 {
        get => parent::$prop2::get();
        set { parent::$prop2::set($value); }
    }
}

$test = new Test;

try {
    $test->prop = 0;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

try {
    var_dump($test->prop);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

$child = new Child();
$child->prop2 = 43;
var_dump($child->prop2);

?>
--EXPECT--
Must not access backing value of property Test::$prop outside its corresponding hooks
Must not access backing value of property Test::$prop outside its corresponding hooks
int(43)
