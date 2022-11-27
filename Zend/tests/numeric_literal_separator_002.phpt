--TEST--
Invalid use: trailing underscore
--FILE--
<?php
100_;
?>
--EXPECTF--
Parse error: syntax error, unexpected token "_" in %s on line %d
