--TEST--
Hooks accept method-targeted attributes
--FILE--
<?php

#[Attribute]
class A {}

#[Attribute(Attribute::TARGET_METHOD)]
class B {}

class C {
    public $prop {
        #[A] get {}
        #[B] set {}
    }
}

$getAttr = (new ReflectionProperty(C::class, 'prop'))->getGet()->getAttributes()[0];
var_dump($getAttr->getName());
var_dump($getAttr->getArguments());
var_dump($getAttr->newInstance());

$setAttr = (new ReflectionProperty(C::class, 'prop'))->getSet()->getAttributes()[0];
var_dump($setAttr->getName());
var_dump($setAttr->getArguments());
var_dump($setAttr->newInstance());

?>
--EXPECT--
string(1) "A"
array(0) {
}
object(A)#2 (0) {
}
string(1) "B"
array(0) {
}
object(B)#3 (0) {
}
