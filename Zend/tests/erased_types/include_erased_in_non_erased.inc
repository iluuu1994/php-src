<?php

declare(types='erased');

function test(string $p1, string $p2 = 'p2') {
    var_dump($p1, $p2);
}

class P {
    public function test(string $p): string {
        return $p;
    }
}

trait T {
    public function test2(string $p): string {
        return $p;
    }
}
