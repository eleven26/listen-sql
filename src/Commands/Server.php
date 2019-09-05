<?php

namespace Eleven26\ListenSql\Commands;

use Illuminate\Console\Command;

class Server extends Command
{
    public $signature = 'listenSql:start';

    /**
     * Server socket resource.
     *
     * @var resource
     */
    private $sock;

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
        if ($this->isDisable()) {
            $this->error("Listen sql is disabled.");
            $this->info("可在 .env 添加 'LISTEN_SQL_ENABLE=true' 以启用该扩展包.");
            exit;
        }

        $this->init();
        $this->accept();
    }

    /**
     * Create socket and listen
     */
    private function init()
    {
        $this->createSock();
        $this->socketBind();
        $this->socketListen();
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
        $bound = socket_bind($this->sock, $this->ip(), $this->port());

        if (!$bound) {
            $this->throwError();
        }
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

            while ($input = socket_read($client, 1024)) {
                echo $input . PHP_EOL;
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
     * Determine if package is disabled.
     *
     * @return bool
     */
    private function isDisable()
    {
        return !config('listen-sql.listen_sql_enable');
    }

    /**
     * Get ip address for listening
     *
     * @return string
     */
    private function ip()
    {
        return config('listen-sql.listen_sql_bind_address');
    }

    /**
     * Get port for server socket binding.
     *
     * @return int
     */
    private function port()
    {
        return config('listen-sql.listen_sql_bind_port');
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
