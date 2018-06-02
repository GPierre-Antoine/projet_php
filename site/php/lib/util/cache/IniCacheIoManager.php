<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 13:53
 */

namespace util\cache;


use container\Collection;

class IniCacheIoManager implements CacheIoManager
{
    private $filename;
    /** @var Collection */
    private $content;
    private $changed;

    /**
     * IniCacheIoManager constructor.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            touch($filename);
        }
        $this->filename = $filename;
    }

    public function purge()
    {
        $this->content = null;
    }

    public function offsetExists($offset)
    {
        $this->makeSureCacheIsRead();
        $offset = self::quickHash($offset);

        return $this->content->offsetExists($offset);
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

        $holder = parse_ini_file($this->filename);
        if (!is_array($holder)) {
            throw new \RuntimeException('Error when reading cache');
        }
        $this->content = new Collection($holder);
        $this->changed = false;
    }

    private static function quickHash($string)
    {
        return crc32($string);
    }

    public function offsetGet($offset)
    {
        $this->makeSureCacheIsRead();
        $offset = self::quickHash($offset);

        return $this->content->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->makeSureCacheIsRead();
        if (is_null($offset)) {
            throw new \RuntimeException("Cache key can't be null");
        }
        $offset = self::quickHash($offset);
        if (!$this->content->hasKey($offset) || $this->content[$offset] !== $value) {
            $this->changed = true;
            $this->content->offsetSet($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        $this->makeSureCacheIsRead();
        $offset = self::quickHash($offset);
        if (!$this->content->hasKey($offset)) {
            return;
        }
        $this->content->offsetUnset($offset);
        $this->changed = true;
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
        $content = self::array_to_ini($this->content);
        file_put_contents($this->filename, $content);
    }

    public function assertCacheIsRead()
    {
        if (is_null($this->content)) {
            throw new \RuntimeException("Cache not read");
        }
    }

    private static function array_to_ini($iterable)
    {
        $out = '';
        foreach ($iterable as $k => $v) {
            $out .= "$k=$v".PHP_EOL;
        }

        return $out;
    }
}