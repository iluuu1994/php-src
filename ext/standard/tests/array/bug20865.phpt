--TEST--
Bug #20865 (array_key_exists and NULL key)
--FILE--
<?php
    $ta = array(1, 2, 3);
    $ta[NULL] = "Null Value";

    var_dump(array_key_exists(NULL, $ta));
?>
--EXPECTF--
Warning: Implicit array offset coercion from null to string in %s on line %d
bool(true)
