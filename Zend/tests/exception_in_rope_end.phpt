--TEST--
Exception during rope finalization
--FILE--
<?php

set_error_handler(function() { throw new Exception; }, promote_to_exception: true);

try {
    $b = "foo";
    $str = "y$b$a";
} catch (Exception $e) {
    echo "Exception\n";
}

?>
--EXPECT--
Exception
