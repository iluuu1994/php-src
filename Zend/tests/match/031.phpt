--TEST--
Test warning on duplicate match conditions
--FILE--
<?php

match (1) {
    1, 2, 3, 4, 5, 5 => null,
}

echo match (1) {
    1, 2, 3, 4, 5 => null,
    5 => null,
};

echo match ('1') {
    '1', '2', '3', '4', '5', '5' => null,
};

echo match ('1') {
    '1', '2', '3', '4', '5' => null,
    '5' => null,
};

--EXPECTF--
Warning: Duplicate match condition 5 in %s031.php on line 3

Warning: Duplicate match condition 5 in %s031.php on line 8

Warning: Duplicate match condition "5" in %s031.php on line 12

Warning: Duplicate match condition "5" in %s031.php on line 17
