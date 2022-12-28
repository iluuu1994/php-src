--TEST--
Test not-null assertion operator
--FILE--
<?php

$foo = 42;
var_dump($foo!);

$foo = null;
try {
    var_dump($foo!);
} catch (\Throwable $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
int(42)
Encountered unexpected null value
