--TEST--
Test ReflectionProperty::isWritable()
--FILE--
<?php

class A {
    public $a;
    protected $b;
    private $c;
    public protected(set) int $d;
    public $e { get => 42; }
    public $f { set {} }
    public readonly int $g;
    public private(set) int $h;
    public public(set) readonly int $i;

    public function setG($g) {
        $this->g = $g;
    }

    public function __clone() {
        $rp = new ReflectionProperty(A::class, 'g');
        echo $rp->getName() . ' from static (initialized, clone): ';
        var_dump($rp->isWritable($this));
    }
}

class B extends A {
    public function __set($name, $value) {}
}

$test = static function ($scope) {
    $rc = new ReflectionClass(A::class);
    foreach ($rc->getProperties() as $rp) {
        echo $rp->getName() . ' from ' . ($scope ?? 'global') . ': ';
        var_dump($rp->isWritable(new A(), $scope));

        if ($rp->name == 'g') {
            $a = new A();
            $a->setG(42);
            echo $rp->getName() . ' from ' . ($scope ?? 'global') . ' (initialized): ';
            var_dump($rp->isWritable($a, $scope));
            clone $a;
        }

        echo $rp->getName() . ' from ' . ($scope ?? 'global') . ' (null): ';
        var_dump($rp->isWritable(null, $scope));
    }
};

$test('A');
$test->bindTo(null, 'A')('static');

$test(null);
$test->bindTo(null, null)('static');

$rp = new ReflectionProperty('A', 'i');
$a = new A();
echo $rp->getName() . ' from global (uninitialized): ';
var_dump($rp->isWritable($a));
$a->i = 42;
echo $rp->getName() . ' from global (initialized): ';
var_dump($rp->isWritable($a));

$rp = new ReflectionProperty('B', 'i');
$b = new B();
echo $rp->getName() . ' from global (uninitialized): ';
var_dump($rp->isWritable($b));
unset($b->a);
echo $rp->getName() . ' from global (unset): ';
var_dump($rp->isWritable($b));

?>
--EXPECT--
a from A: bool(true)
a from A (null): bool(true)
b from A: bool(true)
b from A (null): bool(true)
c from A: bool(true)
c from A (null): bool(true)
d from A: bool(true)
d from A (null): bool(true)
e from A: bool(false)
e from A (null): bool(false)
f from A: bool(true)
f from A (null): bool(true)
g from A: bool(true)
g from A (initialized): bool(false)
g from static (initialized, clone): bool(true)
g from A (null): bool(true)
h from A: bool(true)
h from A (null): bool(true)
i from A: bool(true)
i from A (null): bool(true)
a from static: bool(true)
a from static (null): bool(true)
b from static: bool(true)
b from static (null): bool(true)
c from static: bool(true)
c from static (null): bool(true)
d from static: bool(true)
d from static (null): bool(true)
e from static: bool(false)
e from static (null): bool(false)
f from static: bool(true)
f from static (null): bool(true)
g from static: bool(true)
g from static (initialized): bool(false)
g from static (initialized, clone): bool(true)
g from static (null): bool(true)
h from static: bool(true)
h from static (null): bool(true)
i from static: bool(true)
i from static (null): bool(true)
a from global: bool(true)
a from global (null): bool(true)
b from global: bool(false)
b from global (null): bool(false)
c from global: bool(false)
c from global (null): bool(false)
d from global: bool(false)
d from global (null): bool(false)
e from global: bool(false)
e from global (null): bool(false)
f from global: bool(true)
f from global (null): bool(true)
g from global: bool(false)
g from global (initialized): bool(false)
g from static (initialized, clone): bool(true)
g from global (null): bool(false)
h from global: bool(false)
h from global (null): bool(false)
i from global: bool(true)
i from global (null): bool(true)
a from static: bool(true)
a from static (null): bool(true)
b from static: bool(false)
b from static (null): bool(false)
c from static: bool(false)
c from static (null): bool(false)
d from static: bool(false)
d from static (null): bool(false)
e from static: bool(false)
e from static (null): bool(false)
f from static: bool(true)
f from static (null): bool(true)
g from static: bool(false)
g from static (initialized): bool(false)
g from static (initialized, clone): bool(true)
g from static (null): bool(false)
h from static: bool(false)
h from static (null): bool(false)
i from static: bool(true)
i from static (null): bool(true)
i from global (uninitialized): bool(true)
i from global (initialized): bool(false)
i from global (uninitialized): bool(true)
i from global (unset): bool(true)
