--TEST--
Bug #60110 (fprintf() propagates written bytes)
--FILE--
<?php

class Stream {
    public $context;
    public bool $firstWrite = true;

    function stream_open($path, $mode, $options, &$opened_path) {
        return true;
    }

    function stream_read($count) {}

    function stream_write($data) {
        if ($this->firstWrite) {
            $this->firstWrite = false;
            return 3;
        }
        return 0;
    }

    function stream_tell() {}

    function stream_eof() {
        return true;
    }

    function stream_seek($offset, $whence) {
        return false;
    }
}

if (!stream_wrapper_register('myStream', 'Stream')) {
    die('Failed to register stream');
}

$f = fopen('myStream://test', 'w+');
var_dump(fprintf($f, "Hello world"));
fclose($f);

$f = fopen('myStream://test', 'w+');
var_dump(vfprintf($f, "Hello world", []));
fclose($f);

?>
--EXPECT--
int(3)
int(3)
