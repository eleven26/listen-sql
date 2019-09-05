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


## 配置

在 `.env` 文件最后添加下面这一行（默认不启用）:

```
LISTEN_SQL_ENABLE=true
```


## 使用

```php
php artisan listenSql:start
```

到这一步，去页面刷新的时候，就可以在控制台看到 `sql` 语句了
