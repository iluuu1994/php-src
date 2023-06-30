<?php

$strings_array = ["", "12345", "abcdef", "123abc", "_123abc"];

for ($i = 0; $i < 10000; $i++) {
    foreach ($strings_array as $str) {
        substr($str, 1);
        substr($str, 0);
        substr($str, -2);
        substr($str, 1, 3);
        substr($str, 1, 0);
        substr($str, 1, -3);
        substr($str, 0, 3);
        substr($str, 0, 0);
        substr($str, 0, -3);
        substr($str, -2, 3);
        substr($str, -2, 0 );
        substr($str, -2, -3);
    }
    substr("abcde", 2, -2);
    substr("abcde", -3, -2);
    substr("abcdef", 4, -4);
    substr("abc\x0xy\x0z", 2);
    substr('\xIñtërnâtiônàlizætiøn', 3);
    substr("abcd", -8);
    substr("abcdef", 2);
    substr("abcdef", 2, NULL);
}
