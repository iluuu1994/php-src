--TEST--
errmsg: __unset() must take exactly 1 argument
--FILE--
<?php

class test {
    function __unset() {
    }
}

echo "Done\n";
?>
--EXPECTF--
Fatal error: Method test::__unset() must take between 1 and 2 arguments in %s on line %d
