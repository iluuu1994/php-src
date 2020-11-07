--TEST--
Test nested === operator overloading
--FILE--
<?php

class Box
{
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public static function __isIdentical(object $lhs, object $rhs): bool
    {
        return $lhs->value === $rhs->value;
    }
}

$box1 = new Box(new Box(1));
$box2 = new Box(new Box(1));
$box3 = new Box(new Box(2));

var_dump($box1 === $box2);
var_dump($box1 === $box3);
var_dump(match ($box1) {
    $box3 => '$box3',
    $box2 => '$box2',
});
var_dump(in_array($box1, [$box2], true));
var_dump(in_array($box1, [$box3], true));

?>
--EXPECT--
bool(true)
bool(false)
string(5) "$box2"
bool(true)
bool(false)
