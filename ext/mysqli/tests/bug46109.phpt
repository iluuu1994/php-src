--TEST--
Bug #46109 (MySQLi::init - Memory leaks)
--EXTENSIONS--
mysqli
--SKIPIF--
<?php
require_once 'skipifconnectfailure.inc';
?>
--FILE--
<?php
require_once 'connect.inc';

$mysqli = new mysqli();
$mysqli->init();
$mysqli->init();
echo "done";
?>
--EXPECTF--
Deprecated: Method mysqli::init() is deprecated since 8.1, replace calls to parent::init() with parent::__construct() in %s on line %d

Deprecated: Method mysqli::init() is deprecated since 8.1, replace calls to parent::init() with parent::__construct() in %s on line %d
done
