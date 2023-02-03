--TEST--
Basic generic arrays
--FILE--
<?php

function f1(): array<int, string> {}
function f2(): array<int, array<int, string>> {}

echo (new \ReflectionFunction('f1'))->getReturnType(), "\n";
echo (new \ReflectionFunction('f2'))->getReturnType(), "\n";

?>
--EXPECT--
array<int, string>
array<int, array<int, string>>
