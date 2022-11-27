--TEST--
Binding pattern
--FILE--
<?php

class Box {
    public function __construct(
        public $value,
    ) {}
}

class NotBox {
    public function __construct(
        public $value,
    ) {}
}

var_dump(10 is $a);
var_dump($a);

var_dump('Hello world' is $a);
var_dump($a);

var_dump(new Box(42) is Box { value: $a });
var_dump($a);

var_dump(new NotBox(43) is Box { value: $a });
var_dump($a);

var_dump(43 is $a @ int);
var_dump($a);

var_dump([] is $a @ string);
var_dump($a);

?>
--EXPECT--
bool(true)
int(10)
bool(true)
string(11) "Hello world"
bool(true)
int(42)
bool(false)
int(42)
bool(true)
int(43)
bool(false)
int(43)
