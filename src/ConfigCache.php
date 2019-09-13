<?php

namespace Eleven26\ListenSql;

trait ConfigCache
{
    /**
     * Set server status to running.
     */
    public function running()
    {
        file_put_contents(self::statusPath(), 1);
    }

    /**
     * Determine server is in running state.
     *
     * @return bool
     */
    public static function serverIsRunning()
    {
        return @file_get_contents(self::statusPath()) == 1;
    }

    /**
     * Cache currently listening port.
     *
     * @param int $port
     */
    public function cachePort($port)
    {
        file_put_contents($this->portPath(), $port);
    }

    /**
     * Get currently listening port.
     *
     * @return int
     */
    public function getCachePort()
    {
        return @file_get_contents($this->portPath());
    }

    /**
     * Cache listening ip address.
     *
     * @param string $ip
     */
    public function cacheIp($ip)
    {
        \Cache::forever($this->ipKey(), $ip);
    }

    /**
     * Get listening ip address.
     *
     * @return string
     */
    public function getCacheIp()
    {
        return \Cache::get($this->ipKey(), '127.0.0.1');
    }

    /**
     * Clear all caches.
     */
    public function clearCache()
    {
        @unlink(self::statusPath());
        @unlink(self::portPath());
    }

    /**
     * Server status cache saving path.
     *
     * @return string
     */
    private static function statusPath()
    {
        return storage_path('logs/listen-sql');
    }

    /**
     * Server ip cache key.
     *
     * @return string
     */
    private function ipKey()
    {
        return 'listen-sql:ip';
    }

    /**
     * Listening port saving path.
     *
     * @return string
     */
    private function portPath()
    {
        return storage_path('logs/listen-sql-port');
    }
}
