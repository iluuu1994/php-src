--TEST--
Match blocks
--FILE--
<?php

function throw_($value) {
    var_dump([new \stdClass] + match ($value) {
        1 { throw new Exception('Exception with live var'); [] },
    });
}

function return_($value) {
    var_dump([new \stdClass] + match ($value) {
        1 { return 42; [] },
    });
}

function goto_($value) {
    var_dump([new \stdClass] + match ($value) {
        1 { goto end; [] },
    });
end:
    return 42;
}

try {
    throw_(1);
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

var_dump(return_(1));
var_dump(goto_(1));

?>
--EXPECT--
Exception with live var
int(42)
int(42)
