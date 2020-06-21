--TEST--
Inlined partials.
--FILE--
<?php

$dump = var_dump(1, ?, ?, 4);
$dump(2, 3);

--EXPECT--
int(1)
int(2)
int(3)
int(4)
