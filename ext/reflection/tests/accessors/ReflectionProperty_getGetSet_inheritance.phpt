--TEST--
ReflectionClass::get{Get,Set}() inheritance
--FILE--
<?php

class A {
    public $foo {
        get {
            return 'A::$foo::get';
        }
        set {
            echo "A::\$foo::set\n";
        }
    }
}

class B extends A {
    public $foo {
        get {
            return 'B::$foo';
        }
    }
}

$a = new A();
$b = new B();

echo ((new ReflectionProperty(A::class, 'foo'))->getGet()->invoke($a)), "\n";
echo ((new ReflectionProperty(A::class, 'foo'))->getGet()->invoke($b)), "\n";
echo ((new ReflectionProperty(B::class, 'foo'))->getGet()->invoke($b)), "\n";

((new ReflectionProperty(A::class, 'foo'))->getSet()->invoke($a, null));
((new ReflectionProperty(A::class, 'foo'))->getSet()->invoke($b, null));
((new ReflectionProperty(B::class, 'foo'))->getSet()->invoke($b, null));

?>
--EXPECT--
A::$foo::get
A::$foo::get
B::$foo
A::$foo::set
A::$foo::set
A::$foo::set
