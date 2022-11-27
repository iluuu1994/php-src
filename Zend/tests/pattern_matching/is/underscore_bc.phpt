--TEST--
_ can still be a function name
--FILE--
<?php

// Avoid clash with _ in gettext
namespace Foo;

function _() {
    echo "Done\n";
}

_();
_ ();

?>
--EXPECT--
Done
Done
