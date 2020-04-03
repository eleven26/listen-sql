<?php

namespace Eleven26\ListenSql\Commands;

use Eleven26\ListenSql\ConfigCache;
use Illuminate\Console\Command;

class Server extends Command
{
    use ConfigCache;

    /**
     * @var string
     */
    public $signature = 'listen-sql:start {--ip=127.0.0.1}';

    /**
     * Server socket resource.
     *
     * @var resource
     */
    private $sock;

    /**
     * @var string
     */
    private $ip;

    /**
     * Connected client socket resource.
     *
     * @var resource[]
     */
    private $clients = [];

    /**
     * Start socket server listen sql.
     */
    public function handle()
    {
        $this->ip = $this->option('ip');

        $this->createSock();
        $this->socketBind();
        $this->socketListen();

        // Set server running state to 1
        $this->running();

        $this->accept();
    }

    /**
     * Create server socket.
     */
    private function createSock()
    {
        $this->sock = socket_create(AF_INET, SOCK_STREAM, 0);

        if (!$this->sock) {
            $this->throwError();
        }
    }

    /**
     * Bind server socket.
     */
    private function socketBind()
    {
        $bound = false;
        $port = 10000;

        while (!$bound) {
            if ($port > 11000) break;
            $bound = @socket_bind($this->sock, $this->ip(), ++$port);
        }

        if (!$bound) {
            $this->throwError();
        }

        $this->cachePort($port);
        $this->cacheIp($this->ip());
    }

    /**
     * Start listening
     */
    private function socketListen()
    {
        socket_listen($this->sock);
    }

    /**
     * Prepare to accept client connections.
     */
    private function accept()
    {
        while (true) {
            $client = socket_accept($this->sock);
            $this->clients[(int) $client] = $client;
            $this->info("----------".date("Y-m-d H:i:s")."----------");
            while ($input = socket_read($client, 1024)) {
                echo $input;
            }

            socket_close($client);
            unset($this->clients[(int) $client]);
        }
    }

    /**
     * Throw exception when socket error occurs.
     */
    private function throwError()
    {
        $errCode = socket_last_error($this->sock);
        $errMsg = socket_strerror($errCode);

        throw new \RuntimeException("Couldn't create socket: [$errCode]: $errMsg\n");
    }

    /**
     * Get ip address for listening
     *
     * @return string
     */
    private function ip()
    {
        return $this->ip;
    }

    /**
     * Shut down server socket and client sockets when server is close.
     */
    public function __destruct()
    {
        @socket_shutdown($this->sock);
        @socket_close($this->sock);
        $this->clients = [];
    }
}
