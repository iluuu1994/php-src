--TEST--
Basic tests for explicit pass-by-ref
--FILE--
<?php

// Works (by-ref arg)
$a = 42;
incArgRef(&$a);
var_dump($a);

// Works (by-ref arg, deep reference)
writeArgRef(&$b[0][1]);
var_dump($b);

// Works (prefer-ref arg)
$c = 42;
$vars = ['b' => 2, 'a' => 1];
$vars2 = [2, 1];
array_multisort(&$vars, $vars2);
var_dump($vars, $vars2);

// Works (by-ref arg, by-ref function)
$e = 42;
incArgRef(&returnsRef($e));
var_dump($e);

// Fails (by-val arg)
try {
    $f = 1;
    var_dump(incArgVal(&$f));
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

// Fails (by-ref arg, by-val function)
try {
    $g = 42;
    incArgRef(&returnsVal($g));
    var_dump($g);
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

// Fails (by-val arg, by-ref function)
try {
    $h = 1;
    var_dump(incArgVal(&returnsRef($h)));
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

// Functions intentionally declared at the end of the file,
// to avoid the fbc being known during compilation
function incArgVal($a) { return $a + 1; }
function incArgRef(&$a) { $a++; }
function writeArgRef(&$a) { $a = 43; }

function returnsVal($a) { return $a; }
function &returnsRef(&$a) { return $a; }

?>
--EXPECT--
int(43)
array(1) {
  [0]=>
  array(1) {
    [1]=>
    int(43)
  }
}
array(2) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(2)
}
array(2) {
  [0]=>
  int(1)
  [1]=>
  int(2)
}
int(43)
Cannot pass reference to by-value parameter 1
Cannot pass result of by-value function by reference
Cannot pass reference to by-value parameter 1
