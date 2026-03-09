--TEST--
CompactVector: basic int type operations
--FILE--
<?php
foreach (["int8", "int16", "int32", "int64"] as $type) {
    echo "--- $type ---\n";
    $v = new CompactVector($type);

    // Append
    $v[] = 1;
    $v[] = 2;
    $v[] = 3;

    var_dump($v[0], $v[1], $v[2]);

    // Overwrite
    $v[1] = 42;
    var_dump($v[1]);

    // Negative values
    $v[0] = -1;
    var_dump($v[0]);
}
?>
--EXPECT--
--- int8 ---
int(1)
int(2)
int(3)
int(42)
int(-1)
--- int16 ---
int(1)
int(2)
int(3)
int(42)
int(-1)
--- int32 ---
int(1)
int(2)
int(3)
int(42)
int(-1)
--- int64 ---
int(1)
int(2)
int(3)
int(42)
int(-1)
