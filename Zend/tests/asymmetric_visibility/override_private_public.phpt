--TEST--
private(set) property is implicitly final
--FILE--
<?php

class A {
	public private(set) string $foo;
}

class B extends A {
	public string $foo;
}

?>
--EXPECT--
foo
