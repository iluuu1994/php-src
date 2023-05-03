--TEST--
Hooks in property promotion
--FILE--
<?php

class Test {
    public function __construct(
        public $prop {
            get {}
            set {}
        }
    ) {}
}

?>
--EXPECTF--
Parse error: syntax error, unexpected token "{", expecting ")" in %s on line %d
