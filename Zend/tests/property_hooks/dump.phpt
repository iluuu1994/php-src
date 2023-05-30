--TEST--
Dumping object with property hooks
--FILE--
<?php

class Test {
    public $addedHooks = 'addedHooks';
    public $virtual {
        get { throw new Exception(); }
    }
    public $backed = 'backed' {
        get { throw new Exception(); }
        set { $field = $value; }
    }
    private $private = 'private' {
        get { throw new Exception(); }
        set { $field = $value; }
    }
}

class Child extends Test {
    public $addedHooks {
        get { throw new Exception(); }
    }
}

function dump($test) {
    var_dump($test);
    var_dump(get_object_vars($test));
    var_export($test);
    echo "\n";
}

dump(new Test);
dump(new Child);

?>
--EXPECT--
object(Test)#1 (3) {
  ["addedHooks"]=>
  string(10) "addedHooks"
  ["backed"]=>
  string(6) "backed"
  ["private":"Test":private]=>
  string(7) "private"
}
array(2) {
  ["addedHooks"]=>
  string(10) "addedHooks"
  ["backed"]=>
  string(6) "backed"
}
\Test::__set_state(array(
   'addedHooks' => 'addedHooks',
   'backed' => 'backed',
   'private' => 'private',
))
object(Child)#1 (3) {
  ["addedHooks"]=>
  string(10) "addedHooks"
  ["backed"]=>
  string(6) "backed"
  ["private":"Test":private]=>
  string(7) "private"
}
array(2) {
  ["addedHooks"]=>
  string(10) "addedHooks"
  ["backed"]=>
  string(6) "backed"
}
\Child::__set_state(array(
   'addedHooks' => 'addedHooks',
   'backed' => 'backed',
   'private' => 'private',
))
