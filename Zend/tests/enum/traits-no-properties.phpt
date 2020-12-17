--TEST--
Enum cannot have properties, even via traits
--SKIPIF--
<?php
die("skip, not yet implemented");
?>
--FILE--
<?php

trait Rectangle {
    protected string $shape = "Rectangle";

    public function shape(): string {
        return $this->shape;
    }
}

enum Suit {
    use Rectangle;

    case Hearts;
    case Diamonds;
    case Clubs;
    case Spades;
}

?>
--EXPECTF--
Fatal error: Traits used in enumerations may not contain properties.
