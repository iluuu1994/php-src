--TEST--
errmsg: __isset() must take exactly 1 argument
--FILE--
<?php

class test {
    function __isset() {
    }
}

echo "Done\n";
?>
--EXPECTF--
Fatal error: Method test::__isset() must take between 1 and 2 arguments in %s on line %d
