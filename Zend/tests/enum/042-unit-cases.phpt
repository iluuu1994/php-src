--TEST--
Unit enums can list cases
--FILE--
<?php

enum Suit {
  case Hearts;
  case Diamonds;
  case Clubs;
  case Spades;
}

var_dump(Suit::cases());

?>
--EXPECTF--
array(4) {
  [0]=>
  enum(Suit::Hearts)
  [1]=>
  enum(Suit::Diamonds)
  [2]=>
  enum(Suit::Clubs)
  [3]=>
  enum(Suit::Spades)
}
