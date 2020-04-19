--TEST--
Error when using result of match statement list
--FILE--
<?php

var_dump(match ('bar') {
    'foo' => 'foo',
    'bar' => 'bar',
    'baz' => {
        var_dump('This should not compile');
    },
});

?>
--EXPECTF--
Fatal error: Match expressions that are not statements can't contain statement lists in %s on line %d
