<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 22:31
 */

namespace container;


class CollectionForwarder implements \ArrayAccess
{

    protected $collection;

    public function __construct(array $array)
    {
        $this->collection = new Collection($array);
    }

    public function offsetExists($offset)
    {
        $offset = self::quickHash($offset);

        return $this->collection->offsetExists($offset);
    }

    public static function quickHash($string)
    {
        return crc32($string);
    }

    public function offsetGet($offset)
    {
        $offset = self::quickHash($offset);

        return $this->collection->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \RuntimeException("No name set for Collection Forwarder offset");
        }
        $offset = self::quickHash($offset);
        $this->collection->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $offset = self::quickHash($offset);
        $this->collection->offsetUnset($offset);
    }
}