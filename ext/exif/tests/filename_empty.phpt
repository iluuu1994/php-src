--TEST--
Passing empty filename to exif_read_data() and exif_thumbnail()
--EXTENSIONS--
exif
--FILE--
<?php

try {
    exif_read_data("");
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

try {
    exif_thumbnail("");
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

try {
    exif_read_data("foo\0bar");
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

try {
    exif_thumbnail("foo\0bar");
} catch (ValueError $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
exif_read_data(): Argument #1 ($file) must not be empty
exif_thumbnail(): Argument #1 ($file) must not be empty
exif_read_data(): Argument #1 ($file) must not contain any null bytes
exif_thumbnail(): Argument #1 ($file) must not contain any null bytes
