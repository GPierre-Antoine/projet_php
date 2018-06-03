<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 13:53
 */

namespace util\cache;


class JsonCacheIoManager implements CacheIoManager
{
    private $filename;
    /** @var array */
    private $content;
    private $changed;

    /**
     * JsonCacheIoManager constructor.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            touch($filename);
            file_put_contents($filename, "{}");
        }
        $this->filename = $filename;

    }

    public function purge()
    {
        $this->content = null;
    }

    public function offsetGet($offset)
    {
        $this->makeSureCacheIsRead();
        $offset = self::quickHash($offset);

        return $this->content[$offset];
    }

    public function makeSureCacheIsRead()
    {
        if (is_null($this->content)) {
            $this->read();
        }
    }

    public function read()
    {
        if (!is_null($this->content)) {
            throw new \RuntimeException('Cache Already Generated');
        }

        $holder = json_decode(file_get_contents($this->filename), true);
        if (!is_array($holder)) {
            throw new \RuntimeException('Error when reading cache');
        }
        $this->content = $holder;
        $this->changed = false;
    }

    private static function quickHash($string)
    {
        return crc32($string);
    }

    public function offsetSet($offset, $value)
    {
        $this->makeSureCacheIsRead();
        if (is_null($offset)) {
            throw new \RuntimeException("Cache key can't be null");
        }
        $offset = self::quickHash($offset);
        if (!self::hasKey($this->content, $offset) || $this->content[$offset] !== $value) {
            $this->changed = true;
            $this->content[$offset] = $value;
        }
    }

    private static function hasKey($array, $key)
    {
        return isset($array[$key]) && !empty($array[$key]);
    }

    public function offsetUnset($offset)
    {
        $this->makeSureCacheIsRead();
        $offset = self::quickHash($offset);
        if (!self::hasKey($this->content, $offset)) {
            return;
        }
        unset($this->content[$offset]);
        $this->changed = true;
    }

    public function offsetExists($offset)
    {
        $this->makeSureCacheIsRead();
        $offset = self::quickHash($offset);

        return self::hasKey($this->content, $offset);
    }

    public function __destruct()
    {
        if ($this->changed) {
            $this->write();
        }
    }

    public function write()
    {
        $this->assertCacheIsRead();
        $content = json_encode($this->content);
        file_put_contents($this->filename, $content);
    }

    public function assertCacheIsRead()
    {
        if (is_null($this->content)) {
            throw new \RuntimeException("Cache not read");
        }
    }
}