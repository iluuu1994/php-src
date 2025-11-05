--TEST--
Or pattern
--FILE--
<?php

var_dump(0 is 0 ..< 10);
var_dump(5 is 0 ..< 10);
var_dump(10 is 0 ..< 10);
var_dump(11 is 0 ..< 10);
var_dump(0 is 0 ..= 10);
var_dump(5 is 0 ..= 10);
var_dump(10 is 0 ..= 10);
var_dump(11 is 0 ..= 10);

var_dump('a' is 'a' ..< 'c');
var_dump('b' is 'a' ..< 'c');
var_dump('c' is 'a' ..< 'c');
var_dump('d' is 'a' ..< 'c');
var_dump('a' is 'a' ..= 'c');
var_dump('b' is 'a' ..= 'c');
var_dump('c' is 'a' ..= 'c');
var_dump('d' is 'a' ..= 'c');

?>
--EXPECT--
bool(true)
bool(true)
bool(false)
bool(false)
bool(true)
bool(true)
bool(true)
bool(false)
bool(true)
bool(true)
bool(false)
bool(false)
bool(true)
bool(true)
bool(true)
bool(false)
