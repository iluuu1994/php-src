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
Fatal error: Hooked property must not be promoted in %s on line %d
