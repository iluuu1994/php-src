<?php

$host = '0.0.0.0';
$port = 1234;
$outputFile = __DIR__ . '/received_data.txt';

$serverSocket = stream_socket_server("tcp://$host:$port", $errno, $errstr);
if (!$serverSocket) {
    die("Error: $errstr ($errno)\n");
}
stream_set_blocking($serverSocket, false); // Non-blocking server socket

$clients = [];

echo "Server listening on $host:$port...\n";

while (true) {
    $readSockets = [$serverSocket];
    $readSockets = array_merge($readSockets, $clients);

    $write = $except = null;
    // Wait for activity on any socket
    if (stream_select($readSockets, $write, $except, 0, 200000)) {
        foreach ($readSockets as $socket) {
            if ($socket === $serverSocket) {
                // Accept new connection
                $newClient = stream_socket_accept($serverSocket, 0);
                if ($newClient) {
                    stream_set_blocking($newClient, false); // Non-blocking client
                    $clients[] = $newClient;
                    echo "New connection accepted\n";
                }
            } else {
                // Read from an existing client
                $data = fread($socket, 1024);
                if ($data === '' || $data === false) {
                    echo "Client disconnected\n";
                    fclose($socket);
                    $clients = array_filter($clients, fn($c) => $c !== $socket);
                } else {
                    file_put_contents($outputFile, $data, FILE_APPEND);
                }
            }
        }
    }

    // Small delay to prevent high CPU usage
    usleep(10000);
}
