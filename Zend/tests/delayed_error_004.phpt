--TEST--
Delayed errors: The first error promoted to exception takes precedence
--FILE--
<?php

set_error_handler(function ($errno, $errstr) {
    throw new ErrorException("Warning: $errstr");
}, promote_to_exception: true);

try {
    class C extends DateTime {
        /* Generates one warning per method. The warning for the first method
         * takes precedence. */
        public function getTimezone() {}
        public function getTimestamp() {}
    };
} catch (Exception $e) {
    printf("%s: %s\n", $e::class, $e->getMessage());
}

?>
--EXPECT--
ErrorException: Warning: Return type of C::getTimezone() should either be compatible with DateTime::getTimezone(): DateTimeZone|false, or the #[\ReturnTypeWillChange] attribute should be used to temporarily suppress the notice
