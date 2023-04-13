--TEST--
magic_method_get_calling_scope() from main
--FILE--
<?php
try {
    magic_method_get_calling_scope();
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
magic_method_get_calling_scope() must be called from one of __get(), __set(), __isset(), __unset(), __clone(), __call() or __callStatic() magic methods.
