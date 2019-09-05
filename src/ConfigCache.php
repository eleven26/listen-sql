<?php

namespace Eleven26\ListenSql;

trait ConfigCache
{
    /**
     * Set server status to running.
     */
    public function running()
    {
        \Cache::forever(static::statusKey(), 1);
    }

    /**
     * Determine server is in running state.
     *
     * @return bool
     */
    public static function serverIsRunning()
    {
        return \Cache::get(static::statusKey()) == 1;
    }

    /**
     * Cache currently listening port.
     *
     * @param int $port
     */
    public function cachePort($port)
    {
        \Cache::forever($this->portKey(), $port);
    }

    /**
     * Get currently listening port.
     *
     * @return int
     */
    public function getCachePort()
    {
        return \Cache::get($this->portKey());
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
        \Cache::forget($this->statusKey());
        \Cache::forget($this->portKey());
    }

    /**
     * Server Status cache key in cache.
     *
     * @return string
     */
    private static function statusKey()
    {
        return 'listen-sql:listening';
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
     * Listening port key in cache.
     *
     * @return string
     */
    private function portKey()
    {
        return 'listen-sql:listening-port';
    }
}
