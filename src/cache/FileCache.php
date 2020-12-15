<?php


namespace sf\cache;

class FileCache implements CacheInterface
{
    /**
     * @var string the directory to store cache files.
     * 缓存文件的地址，例如/Users/jun/projects/www/simple-framework/runtime/cache/
     */
    public $cachePath;
    /**
     * Builds a normalized cache key from a given key.
     */
    public function buildKey($key)
    {
        if (!is_string($key)) {
            // 不是字符串就json_encode一把，转成字符串，也可以用其他方法
            $key = json_encode($key);
        }
        return md5($key);
    }

    /**
     * Retrieves a value from cache with a specified key.
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // filemtime用来获取文件的修改时间
        if (@filemtime($cacheFile) > time()) {
            // file_get_contents用来获取文件内容，unserialize用来反序列化文件内容
            return unserialize(@file_get_contents($cacheFile));
        } else {
            return false;
        }
    }

    /**
     * Checks whether a specified key exists in the cache.
     */
    public function exists($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // 用修改时间标记过期时间，存入时会做相应的处理
        return filemtime($cacheFile) > time();
    }

    /**
     * Retrieves multiple values from cache with the specified keys.
     */
    public function mget($keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        return $results;
    }

    /**
     * Stores a value identified by a key into cache.
     */
    public function set($key, $value, $duration = 0)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // serialize用来序列化缓存内容
        $value = serialize($value);
        // file_put_contents用来将序列化之后的内容写入文件，LOCK_EX表示写入时会对文件加锁
        if (file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            if ($duration <= 0) {
                // 不设置过期时间，设置为一年，这是因为用文件的修改时间来做过期时间造成的
                // redis/memcache 等都不会有这个问题
                $duration = 31536000; // 1 year
            }
            // touch用来设置修改时间，过期时间为当前时间加上$duration
            return touch($cacheFile, $duration + time());
        } else {
            return false;
        }
    }

    /**
     * Stores multiple items in cache. Each item contains a value identified by a key.
     */
    public function mset($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->set($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     */
    public function add($key, $value, $duration = 0)
    {
        //  key不存在，就设置缓存
        if (!$this->exists($key)) {
            return $this->set($key, $value, $duration);
        } else {
            return false;
        }
    }

    /**
     * Stores multiple items in cache. Each item contains a value identified by a key.
     */
    public function madd($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->add($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * Deletes a value with the specified key from cache
     */
    public function delete($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // unlink用来删除文件
        return unlink($cacheFile);
    }

    /**
     * Deletes all values from cache.
     * Be careful of performing this operation if the cache is shared among multiple applications.
     * @return boolean whether the flush operation was successful.
     */
    public function flush()
    {
        // 打开cache文件所在目录
        $dir = dir($this->cachePath);

        // 列出目录中的所有文件
        while (($file = $dir->read()) !== false) {
            if ($file !== '.' && $file !== '..') {
                unlink($this->cachePath . $file);
            }
        }

        // 关闭目录
        $dir->close();
    }
}