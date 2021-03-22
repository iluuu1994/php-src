--TEST--
Accessor AST printing
--FILE--
<?php

try {
    assert(false && new class {
        public $prop1 { get; set; }
        public $prop2 {
            get {
                return 42;
            }
            private set {
                echo 'Foo';
                $this->prop1 = 42;
            }
        }
        public $prop3 = 1 { get; private set; }
    });
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
assert(false && new class {
    public $prop1 { get; set; }
    public $prop2 {
        get {
            return 42;
        }
        private set {
            echo 'Foo';
            $this->prop1 = 42;
        }
    }
    public $prop3 = 1 { get; private set; }
})
