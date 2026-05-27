--TEST--
ZE2 __toString() in __destruct
--FILE--
<?php

class Test
{
    function __toString()
    {
        return "Hello\n";
    }

    function __destruct()
    {
        echo $this;
    }
}

$o = new Test;
$o = NULL;
(function () {})();

$o = new Test;

?>
====DONE====
--EXPECT--
Hello
====DONE====
Hello
