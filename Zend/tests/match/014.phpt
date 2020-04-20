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
Fatal error: Match block must return a value. Did you mean to omit the last semicolon? in %s on line %d
