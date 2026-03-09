--TEST--
CompactVector: serialization is not allowed
--FILE--
<?php
$v = new CompactVector("int32");
$v[] = 1;

try {
    serialize($v);
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
Serialization of 'CompactVector' is not allowed
