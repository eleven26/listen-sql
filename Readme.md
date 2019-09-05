# listen-sql

> 一个在控制台看到实时 sql 操作的工具

## 安装

1.通过 [composer](https://getcomposer.org/) 安装 ([eleven26/listen-sql](https://packagist.org/packages/eleven26/listen-sql))。

```bash
composer require "eleven26/listen-sql:~1.0.0"
```

2.注册 Service Provider

- `Laravel`: 修改文件 `config/app.php`，`Laravel 5.5+` 不需要
    ```php
    'providers' => [
        //...
        Eleven26\ListenSql\ListenSqlServiceProvider::class,
    ],
    ```

- `Lumen`: 修改文件 `bootstrap/app.php`
    ```php
    $app->register(Eleven26\ListenSql\ListenSqlServiceProvider::class);
    ```


## 配置 (默认不启用)

在 `.env` 文件最后添加下面这一行:

```
LISTEN_SQL_ENABLE=true
```


## 使用

```php
php artisan listenSql:start
```

到这一步，去页面刷新的时候，就可以在控制台看到 `sql` 语句了


## 可选配置

如需修改默认配置，需要在 `.env` 添加下面配置项:

LISTEN_SQL_BIND_ADDRESS: 绑定的网卡

LISTEN_SQL_BIND_PORT: 监听的端口

LISTEN_SQL_ENABLE: 是否启用该功能


## 默认配置

```
LISTEN_SQL_BIND_ADDRESS=127.0.0.1
LISTEN_SQL_BIND_PORT=10099
LISTEN_SQL_ENABLE=false
```