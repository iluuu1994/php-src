--TEST--
CompactVector: basic float/double operations
--FILE--
<?php
foreach (["float", "double"] as $type) {
    echo "--- $type ---\n";
    $v = new CompactVector($type);

    $v[] = 3.14;
    $v[] = -1.5;
    $v[] = 42;  // int coerced to float

    var_dump($v[0]);
    var_dump($v[1]);
    var_dump($v[2]);
}
?>
--EXPECTF--
--- float ---
float(3.14%S)
float(-1.5)
float(42)
--- double ---
float(3.14%S)
float(-1.5)
float(42)
