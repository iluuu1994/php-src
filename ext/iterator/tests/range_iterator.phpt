--TEST--
RangeIterator
--FILE--
<?php

$iter = Iterator\range(1, 5);

echo "From $iter->from to $iter->to\n";

foreach ($iter as $key => $current) {
    echo "$key => $current\n";
    assert($key == $iter->key);
    assert($current == $iter->current);
}

?>
--EXPECT--
From 1 to 5
0 => 1
1 => 2
2 => 3
3 => 4
4 => 5
