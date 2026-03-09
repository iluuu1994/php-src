--TEST--
CompactVector: integer overflow throws ValueError
--FILE--
<?php
// int8 overflow
$v = new CompactVector("int8");
try {
    $v[] = 128;
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}
try {
    $v[] = -129;
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

// int16 overflow
$v = new CompactVector("int16");
try {
    $v[] = 32768;
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

// int32 overflow
$v = new CompactVector("int32");
try {
    $v[] = 2147483648;
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

// Boundary values that should succeed
$v = new CompactVector("int8");
$v[] = 127;
$v[] = -128;
var_dump($v[0], $v[1]);
?>
--EXPECT--
Value 128 is out of range for int8 (-128 to 127)
Value -129 is out of range for int8 (-128 to 127)
Value 32768 is out of range for int16 (-32768 to 32767)
Value 2147483648 is out of range for int32 (-2147483648 to 2147483647)
int(127)
int(-128)
