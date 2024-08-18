--TEST--
Private class reflection
--FILE--
<?php

namespace {
    function test($c) {
        $r = new ReflectionClass($c);
        var_dump($r->getName());
        var_dump($r->inNamespace());
        var_dump($r->getNamespaceName());
        var_dump($r->getShortName());
    }
}

namespace {
    private class C {}
    test(C::class);
}

namespace Ns {
    private class C {}
    test(C::class);
}

?>
--EXPECTF--
string(%d) "C@%s"
bool(false)
string(%d) ""
string(%d) "C@%s"
string(%d) "Ns\C@%s"
bool(true)
string(%d) "Ns"
string(%d) "C@%s"
