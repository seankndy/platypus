<?php
namespace SeanKndy\Platypus;

class Client {
    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var string
     */
    protected $username, $password;

    public function __construct(string $host, int $port, string $username, string $password) {
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
            ]
        ]);

        if (!($this->socket = stream_socket_client("ssl://$host:$port",
                $errorNumber, $errorString, 15, STREAM_CLIENT_CONNECT, $ctx))) {
            throw new \RuntimeException("Error creating client socket: ($errorNumber) $errorString");
        }

        $this->username = $username;
        $this->password = $password;

        return $this;
    }
    
    public function createRequest(string $action) {
        $req = new Request($this->username, $this->password, $action);
        return $req;
    }

    public function sendRequest(Request $request) {
        $xml = (string)$request;
        $payload = "content-length:" . strlen($xml) . "\r\n\r\n$xml";
        if (fwrite($this->socket, $payload, strlen($payload)) != strlen($payload)) {
            throw new \RuntimeException("Failed to write entire payload to socket!");
        }
        stream_set_timeout($this->socket, 240);
        if (!($contentLengthStr = trim(fgets($this->socket))) || !stristr($contentLengthStr, 'content-length:')) {
            throw new \RuntimeException("Failed to read from socket.");
        }
        $contentLength = intval(trim(substr($contentLengthStr, strpos($contentLengthStr, ':')+1)));

        fgets($this->socket);

        $bytesLeft = $contentLength;
        $respString = '';
        while ($bytesLeft > 0) {
            if (($buf = fread($this->socket, $bytesLeft)) === false) {
                throw new \RuntimeException("Failed to read from socket.");
            }
            $respString .= $buf;
            $bytesLeft -= strlen($buf);
        }

        return Response::fromXml($respString);
    }
}
