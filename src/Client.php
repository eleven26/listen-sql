<?php

namespace Eleven26\ListenSql;

use Illuminate\Database\Events\QueryExecuted;

class Client
{
    use ConfigCache;

    /**
     * @var resource
     */
    private $sock;

    /**
     * @var bool
     */
    private $isConnected = false;

    /**
     * Client constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (!$this->serverIsRunning()) return;

        try {
            $this->createSocket();;
            $this->connect();
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Connection refused')) {
                $this->clearCache();
                return;
            }
            throw $e;
        }

        $this->isConnected = true;
    }

    /**
     * Send data to server.
     *
     * @param string $data
     */
    public function send($data)
    {
        socket_write($this->sock, $data, strlen($data));
    }

    /**
     * Create client socket.
     */
    private function createSocket()
    {
        $this->sock = socket_create(AF_INET, SOCK_STREAM, 0);

        if (!$this->sock) {
            $this->throwError();
        }
    }

    /**
     * Connect to server.
     */
    private function connect()
    {
        $connected = socket_connect($this->sock, $this->ip(), $this->port());

        if (!$connected) {
            $this->throwError();
        }
    }

    /**
     * Throw error when socket error occurs.
     */
    private function throwError()
    {
        $errCode = socket_last_error($this->sock);
        $errMsg = socket_strerror($errCode);

        throw new \RuntimeException("Couldn't create socket: [$errCode]: $errMsg\n");
    }

    /**
     * Get server bounded ip address.
     *
     * @return string
     */
    private function ip()
    {
        return $this->getCacheIp();
    }

    /**
     * Get server bounded ip port.
     *
     * @return int
     */
    private function port()
    {
        return $this->getCachePort();
    }

    /**
     * Create an new socket client from external.
     * Monitor QueryExecuted events.
     */
    public static function createSocketClient()
    {
        $client = app(Client::class);

        // Server is not running.
        if (!$client->isConnected) return;

        \DB::listen(function (QueryExecuted $sql) use ($client) {
            $s = str_replace('?', '%s', $sql->sql);

            $bindings = array_map(function ($binding) use ($client) {
                if (is_string($binding) || is_object($binding)) {
                    return "\"{$binding}\"";
                }

                return $binding;
            }, $sql->bindings);
            $s = sprintf($s, ...$bindings);

            $time = sprintf('%.2f', $sql->time);
            $time = str_pad($time, 8, ' ');

            $sql = sprintf("time: $time %s\r\n", $s);
            $client->send($sql);
        });
    }

    /**
     * Close socket when request finishes.
     */
    public function __destruct()
    {
        @socket_close($this->sock);
    }
}
