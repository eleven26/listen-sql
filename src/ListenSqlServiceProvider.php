<?php

namespace Eleven26\ListenSql;

use Eleven26\ListenSql\Commands\Server;
use Illuminate\Support\ServiceProvider;

class ListenSqlServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->commands([
            Server::class
        ]);

        Client::createSocketClient();
    }
}
