--TEST--
Test long match with undefined variable
--FILE--
<?php

set_error_handler(function ($errno, $message) {
    throw new Exception("Custom error handler: $message");
});

match ($undefVar) {
   default => print("This should not get printed with or without opcache\n"),
   1, 2, 3, 4, 5 => print("Also should not be printed"),
}

print("unreachable");

--EXPECTF--
Fatal error: Uncaught Exception: Custom error handler: Undefined variable $undefVar in %s029.php:4
Stack trace:
#0 %s029.php(7): {closure}(%d, 'Undefined varia...', '%s', %d)
#1 {main}
  thrown in %s029.php on line 4
