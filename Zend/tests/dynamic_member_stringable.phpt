--TEST--
All dynamic members are stringable
--FILE--
<?php

class C
{
    public const C = 'const';
    public $p = 'prop';
    public static $sp = 'static prop';
    
    public function m() {
        return 'method';
    }
    
    public static function sm() {
        return 'static method';
    }
}

class S implements Stringable
{
    public function __construct(public $name) {}
    
    public function __toString() {
        return $this->name;
    }
}

$c = new C;
$var = 42;

// var_dump(C::{new S('C')});
var_dump(C::${new S('sp')});
// var_dump(C::{new S('sm')}());
// var_dump($c->{new S('p')});
// var_dump($c->{new S('m')}());
// var_dump(${new S('var')});

// $foo::{new Stringy()}; // does not work

?>
--EXPECTF--
