--TEST--
Scalar enums reject duplicate string values
--FILE--
<?php

enum Suit: string {
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'H';
}

?>
--EXPECTF--
Fatal error: Duplicate enum case value in %s on line %s
