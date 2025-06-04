<?php

class Client {
    public $socket;
    public $handshaked = false;
    public $nickname = null;

    public function __construct($socket) {
        $this->socket = $socket;
        stream_set_blocking($this->socket, false);
    }
}

$clients = [];

$server = stream_socket_server("tcp://0.0.0.0:8080", $errno, $errstr);
stream_set_blocking($server, false);

echo "✅ WebSocket chat server is running on 0.0.0.0:8080\n";

while (true) {
    $read = [$server];
    foreach ($clients as $client) {
        $read[] = $client->socket;
    }

    $write = $except = [];
    if (stream_select($read, $write, $except, 0, 200000)) {
        foreach ($read as $socket) {
            if ($socket === $server) {
                $conn = stream_socket_accept($server, 0);
                if ($conn) {
                    $clients[(int)$conn] = new Client($conn);
                }
            } else {
                $client = $clients[(int)$socket] ?? null;
                if (!$client) continue;

                $data = fread($socket, 4096);
                if ($data === false || strlen($data) === 0) {
                    removeClient($client);
                    continue;
                }

                if (!$client->handshaked && str_contains($data, 'GET /favicon.ico')) {
                    removeClient($client);
                    continue;
                }

                if (!$client->handshaked) {
                    if (preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $data, $matches)) {
                        $key = trim($matches[1]);
                        $accept = base64_encode(
                            pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11'))
                        );

                        $headers = "HTTP/1.1 101 Switching Protocols\r\n"
                                 . "Upgrade: websocket\r\n"
                                 . "Connection: Upgrade\r\n"
                                 . "Sec-WebSocket-Accept: $accept\r\n\r\n";

                        fwrite($socket, $headers);
                        $client->handshaked = true;
                    }
                    continue;
                }

                $decoded = decodeWebSocket($data);
                if (!$decoded) continue;

                if (!$client->nickname) {
                    $client->nickname = htmlspecialchars($decoded);
                    broadcast("🟢 {$client->nickname} joined the chat", $client, $clients);
                } else {
                    broadcast("💬 {$client->nickname}: $decoded", $client, $clients);
                }
            }
        }
    }
}

function removeClient($client) {
    global $clients;
    if ($client->nickname) {
        broadcast("🔴 {$client->nickname} left the chat", $client, $clients);
    }
    fclose($client->socket);
    unset($clients[(int)$client->socket]);
}

function decodeWebSocket(string $data): string {
    $bytes = array_values(unpack('C*', $data));
    if (count($bytes) < 6) return '';

    $isMasked = ($bytes[1] & 0x80) === 0x80;
    $length = $bytes[1] & 0x7F;

    $offset = 2;
    if ($length === 126) {
        $length = ($bytes[2] << 8) + $bytes[3];
        $offset = 4;
    } elseif ($length === 127) {
        return '';
    }

    if ($isMasked) {
        $mask = array_slice($bytes, $offset, 4);
        $offset += 4;
        $dataBytes = array_slice($bytes, $offset, $length);
        $unmasked = '';

        for ($i = 0; $i < $length; $i++) {
            $unmasked .= chr($dataBytes[$i] ^ $mask[$i % 4]);
        }

        return mb_convert_encoding($unmasked, 'UTF-8', 'UTF-8');
    }

    return '';
}

function encodeWebSocket(string $msg): string {
    $msg = mb_convert_encoding($msg, 'UTF-8', 'UTF-8');
    $b1 = 0x81;
    $len = strlen($msg);

    if ($len <= 125) {
        return pack('CC', $b1, $len) . $msg;
    } elseif ($len <= 65535) {
        return pack('CCn', $b1, 126, $len) . $msg;
    } else {
        return ''; // simplified
    }
}

function broadcast(string $message, $from, &$clients) {
    $encoded = encodeWebSocket($message);
    foreach ($clients as $client) {
        if ($client->handshaked) {
            @fwrite($client->socket, $encoded);
        }
    }
    echo "$message\n";
}