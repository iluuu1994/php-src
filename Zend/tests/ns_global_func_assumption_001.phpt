--TEST--
NS global function assumption: basic optimization
--FILE--
<?php

namespace Ns {
    function test() {
        // Unqualified strlen() should be compiled as a direct call to \strlen()
        return strlen("hello");
    }
}

namespace {
    var_dump(Ns\test());
}
--EXPECT--
int(5)
