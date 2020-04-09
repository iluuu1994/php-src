--TEST--
Statement lists in match expression
--FILE--
<?php

match ('foo') {
    'foo' => {
        var_dump('Statement');
        var_dump('list');
    },
};

?>
--EXPECTF--
string(9) "Statement"
string(4) "list"
