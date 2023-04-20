--TEST--
Basic accessor syntax
--FILE--
<?php

class Test {
    public $prop {
        get { }
        set { }
    }
}

?>
--EXPECT--
