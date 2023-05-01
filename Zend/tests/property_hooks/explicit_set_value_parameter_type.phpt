--TEST--
Explicit set property hook $value parameter
--FILE--
<?php

class Test {
    private string $_prop;
    public string $prop {
        get => $this->_prop;
        set(string|array $prop) {
            $this->_prop = is_array($prop) ? join(', ', $prop) : $prop;
        }
    }
}

$test = new Test();
var_dump($test->prop = 'prop');
var_dump($test->prop = ['prop1', 'prop2']);
var_dump($test->prop);

?>
--EXPECT--
string(4) "prop"
array(2) {
  [0]=>
  string(5) "prop1"
  [1]=>
  string(5) "prop2"
}
string(12) "prop1, prop2"
