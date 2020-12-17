--TEST--
Enum can use traits
--FILE--
<?php

trait Rectangle {
  public function shape(): string {
    return "Rectangle";
  }
}

enum Suit {
  use Rectangle;

  case Hearts;
  case Diamonds;
  case Clubs;
  case Spades;

}

print Suit::Hearts->shape() . PHP_EOL;
print Suit::Diamonds->shape() . PHP_EOL;
print Suit::Clubs->shape() . PHP_EOL;
print Suit::Spades->shape() . PHP_EOL;

?>
--EXPECTF--
Rectangle
Rectangle
Rectangle
Rectangle
