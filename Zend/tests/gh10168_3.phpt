--TEST--
GH-10168 (heap-buffer-overflow at zval_undefined_cv): assign typed prop
--XFAIL--
--FILE--
<?php
class Test
{
	static ?Test $a = null;

	public function __construct() {
		if (self::$a === null) {
			var_dump(self::$a = &$this);
		} else {
			var_dump(self::$a = $this);
		}
	}

	function __destruct() {
		var_dump(self::$a);
		self::$a = null;
	}
}
new Test();
new Test();

?>
--EXPECT--
object(Test)#1 (0) {
}
object(Test)#2 (0) {
}
object(Test)#2 (0) {
}
NULL