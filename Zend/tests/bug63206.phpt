--TEST--
Bug #63206 Fully support error_handler stacking, even inside the error_handler
--FILE--
<?php

set_error_handler(function() {
    echo 'First handler' . PHP_EOL;
});

set_error_handler(function() {
    echo 'Second handler' . PHP_EOL;

    set_error_handler(function() {
        echo 'Internal handler' . PHP_EOL;
    });

    global $tmp;
    $triggerInternalNotice++; // warnings while handling the error should go into internal handler
    fwrite($tmp, "."); // handle errors

    restore_error_handler();
});

$tmp = tmpfile();

$triggerNotice1++;
fwrite($tmp, "."); // handle errors
$triggerNotice2++;
fwrite($tmp, "."); // handle errors
?>
--EXPECT--
Second handler
Internal handler
Second handler
Internal handler
