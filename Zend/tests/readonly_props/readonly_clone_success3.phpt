--TEST--
__clone() can't indirectly modify unlocked readonly properties
--FILE--
<?php

class Foo {
    public function __construct(
        public readonly array $bar
    ) {}

    public function __clone()
    {
        try {
            $this->bar['bar'] = 'bar';
        } catch (Error $e) {
            echo $e->getMessage(), "\n";
        }
    }
}

$foo = new Foo([]);
// First call fills the cache slot
var_dump(clone $foo);
var_dump(clone $foo);

?>
--EXPECTF--
Cannot indirectly modify readonly property Foo::$bar
object(Foo)#2 (%d) {
  ["bar"]=>
  array(0) {
  }
}
Cannot indirectly modify readonly property Foo::$bar
object(Foo)#2 (%d) {
  ["bar"]=>
  array(0) {
  }
}
