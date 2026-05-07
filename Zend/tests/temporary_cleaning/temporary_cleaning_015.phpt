--TEST--
Attempt to free invalid structure (result of ROPE_INIT is not a zval)
--FILE--
<?php
set_error_handler(function () {
    throw new Exception();
}, promote_to_exception: true);
$a = [];
$b = "";
try {
     echo "$a$b\n";
} catch (Exception $ex) {
}
?>
DONE
--EXPECT--
DONE
