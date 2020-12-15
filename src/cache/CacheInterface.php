<?php
namespace sf\cache;

/**
 * CacheInterface
 * @author Harry Sun <sunguangjun@126.com>
 */
interface CacheInterface
{
    /**
     * Builds a normalized cache key from a given key.
     */
    public function buildKey($key);

    /**
     * Retrieves a value from cache with a specified key.
     */
    public function get($key);

    /**
     * Checks whether a specified key exists in the cache.
     */
    public function exists($key);

    /**
     * Retrieves multiple values from cache with the specified keys.
     */
    public function mget($keys);

    /**
     * Stores a value identified by a key into cache.
     */
    public function set($key, $value, $duration = 0);

    /**
     * Stores multiple items in cache. Each item contains a value identified by a key.
     */
    public function mset($items, $duration = 0);

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * Nothing will be done if the cache already contains the key.
     */
    public function add($key, $value, $duration = 0);

    /**
     * Stores multiple items in cache. Each item contains a value identified by a key.
     * If the cache already contains such a key, the existing value and expiration time will be preserved.
     */
    public function madd($items, $duration = 0);

    /**
     * Deletes a value with the specified key from cache
     */
    public function delete($key);

    /**
     * Deletes all values from cache.
     */
    public function flush();
}