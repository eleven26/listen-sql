<?php

namespace Eleven26\ListenSql;

use Eleven26\ListenSql\Commands\Server;
use Illuminate\Support\ServiceProvider;

class ListenSqlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/listen-sql.php', 'listen-sql'
        );
    }

    public function boot()
    {
        $this->commands([
            Server::class
        ]);

        Client::createSocketClient();
    }
}
