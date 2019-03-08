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

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @param string $host Hostname/IP of plat API server
     * @param string $port Port of plat API server
     * @param string $username Username to use within createRequest()
     * @param string $password Password to use within createRequest()
     */
    public function __construct(string $host, int $port, string $username, string $password) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Helper method to generate a Request object and graciously pass in the user/pass for the caller.
     *
     * @param string $action Action string of Request (must be supported by Plat API)
     *
     * @return Request
     */
    public function createRequest(string $action) {
        $req = (new Request($this->username, $this->password, $action));
        return $req;
    }

    /**
     * Connect socket to plat API
     *
     * @return void
     */
    protected function connect() {
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
            ]
        ]);
        if (!($this->socket = stream_socket_client("ssl://{$this->host}:{$this->port}",
                $errorNumber, $errorString, 15, STREAM_CLIENT_CONNECT, $ctx))) {
            throw new \RuntimeException("Error creating client socket: ($errorNumber) $errorString");
        }
    }

    /**
     * Send request $request as XML to Plat API
     *
     * @param Request $request The request to send
     *
     * @return Response
     */
    public function sendRequest(Request $request) {
        $this->connect();

        $xml = (string)$request;
        $payload = "content-length:" . strlen($xml) . "\r\n\r\n$xml";
        if (fwrite($this->socket, $payload, strlen($payload)) != strlen($payload)) {
            throw new \RuntimeException("Failed to write entire payload to socket!");
        }
        stream_set_timeout($this->socket, 240);
        if (!($contentLengthStr = fgets($this->socket))) {
            throw new \RuntimeException("Failed to read from socket.");
        }
        if (!stristr($contentLengthStr = trim($contentLengthStr), 'content-length:')) {
            throw new \RuntimeException("Invalid first-line response from Platypus API.");
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
