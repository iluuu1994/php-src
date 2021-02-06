--TEST--
String scalar enums can list cases
--FILE--
<?php

enum Suit: string {
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}

var_dump(Suit::cases());

?>
--EXPECT--
array(4) {
  ["H"]=>
  enum(Suit::Hearts)
  ["D"]=>
  enum(Suit::Diamonds)
  ["C"]=>
  enum(Suit::Clubs)
  ["S"]=>
  enum(Suit::Spades)
}
