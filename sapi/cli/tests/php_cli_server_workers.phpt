--TEST--
Test multiple PHP_CLI_SERVER_WORKERS
--EXTENSIONS--
curl
pcntl
--ENV--
PHP_CLI_SERVER_WORKERS=2
--SKIPIF--
<?php
include "skipif.inc";
if (!function_exists('pcntl_fork')) die('skip fork not available');
?>
--FILE--
<?php

include "php_cli_server.inc";
php_cli_server_start('echo getmypid();');

$multi_handle = curl_multi_init();
$handles = [];

// FIXME: The first process to `accept` will handle the request. We don't have an easy way to force round robin so we
// just trigger a lot of request to reduce the chances of not hitting all workers.
for ($i = 0; $i < 100; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://' . PHP_CLI_SERVER_ADDRESS);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    curl_multi_add_handle($multi_handle, $ch);
    $handles[] = $ch;
}

$active = null;
do {
    $status = curl_multi_exec($multi_handle, $active);
} while ($status === CURLM_CALL_MULTI_PERFORM);

while ($active && $status === CURLM_OK) {
    if (curl_multi_select($multi_handle) != -1) {
        do {
            $status = curl_multi_exec($multi_handle, $active);
        } while ($status == CURLM_CALL_MULTI_PERFORM);
    }
}

foreach ($handles as $handle) {
    curl_multi_remove_handle($multi_handle, $handle);
}
curl_multi_close($multi_handle);

$pids = [];
foreach ($handles as $handle) {
    $pid = curl_multi_getcontent($handle);
    if ($pid === '') {
        throw new Exception('Timeout');
    }
    $pids[] = $pid;
}
$pids = array_unique($pids);

var_dump(count($pids));

?>
--EXPECT--
int(3)
