--TEST--
Test
--CREDITS--
Some Body (handle)
--FILE--
<?php

namespace Ns;

class C {
    public function __toString(): string {
        global $arr;
        $arr = null;
        gc_collect_cycles();
        return "C";
    }
}

$arr = [new C()];
for ($i = 0; $i < 20; $i++) {
    $arr[] = str_repeat("A", 100);
}

// Frameless call: array argument is NOT addref'd
$result = implode(", ", $arr);

?>
--EXPECT--