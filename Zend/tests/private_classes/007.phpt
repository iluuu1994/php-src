--TEST--
Private classes are confined to their namespace block
--FILE--
<?php

namespace Ns {
    private class C {}
    var_dump(new C());
}

namespace Ns {
    private class C {}
    var_dump(new C());
}

namespace Ns {
    var_dump(class_exists(C::class));
}

?>
--EXPECTF--
object(Ns\C@%s)#1 (0) {
}
object(Ns\C@%s)#1 (0) {
}
bool(false)
