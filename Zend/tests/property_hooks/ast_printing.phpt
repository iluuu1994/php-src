--TEST--
Hook AST printing
--FILE--
<?php

try {
    assert(false && new class {
        public $prop1 { get; abstract set; }
        public $prop2 {
            get {
                return parent::$prop1::get();
            }
            set {
                echo 'Foo';
                $this->prop1 = 42;
            }
        }
    });
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
assert(false && new class {
    public $prop1 {
        get;
        abstract set;
    }
    public $prop2 {
        get {
            return parent::$prop1::get();
        }
        set {
            echo 'Foo';
            $this->prop1 = 42;
        }
    }
})
