--TEST--
CompactVector: constructor validation
--FILE--
<?php
// Invalid type string
try {
    new CompactVector("invalid");
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

// Empty type string
try {
    new CompactVector("");
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
CompactVector::__construct(): Argument #1 ($type) must be one of "int8", "int16", "int32", "int64", "float", "double", "bool", or "counted", "invalid" given
CompactVector::__construct(): Argument #1 ($type) must be one of "int8", "int16", "int32", "int64", "float", "double", "bool", or "counted", "" given
