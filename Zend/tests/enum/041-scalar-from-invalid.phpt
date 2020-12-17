--TEST--
Scalar enums reject upcasting from invalid input
--FILE--
<?php

enum Suit: string {
  case Hearts = 'H';
  case Diamonds = 'D';
  case Clubs = 'C';
  case Spades = 'S';
}

var_dump(Suit::from('A'));

?>
--EXPECTF--
Fatal Error: 'A' is not a valid scalar value for 'Suit' enumeration in %s on line %d
