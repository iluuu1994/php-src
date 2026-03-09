--TEST--
CompactVector: out of bounds access throws
--FILE--
<?php
$v = new CompactVector("int32");
$v[] = 1;
$v[] = 2;

// Read out of bounds
try {
    $v[5];
} catch (OutOfBoundsException $e) {
    echo "Read: ", $e->getMessage(), "\n";
}

// Read negative index
try {
    $v[-1];
} catch (OutOfBoundsException $e) {
    echo "Negative read: ", $e->getMessage(), "\n";
}

// Unset out of bounds
try {
    unset($v[5]);
} catch (OutOfBoundsException $e) {
    echo "Unset: ", $e->getMessage(), "\n";
}

// isset out of bounds returns false, does not throw
var_dump(isset($v[5]));
var_dump(isset($v[0]));

// Null coalescing does not throw
var_dump($v[5] ?? "default");
?>
--EXPECT--
Read: Index out of range
Negative read: Index out of range
Unset: Index out of range
bool(false)
bool(true)
string(7) "default"
