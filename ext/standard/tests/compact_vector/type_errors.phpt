--TEST--
CompactVector: type validation errors
--FILE--
<?php
// Wrong value type for int
$v = new CompactVector("int32");
try {
    $v[] = "string";
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Wrong value type for bool
$v = new CompactVector("bool");
try {
    $v[] = 1;
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Wrong value type for float
$v = new CompactVector("float");
try {
    $v[] = "string";
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Wrong value type for counted
$v = new CompactVector("counted");
try {
    $v[] = 42;
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
CompactVector<int32> value must be of type int, string given
CompactVector<bool> value must be of type bool, int given
CompactVector<float> value must be of type int|float, string given
CompactVector<counted> value must be of type string|array|object|null, int given
