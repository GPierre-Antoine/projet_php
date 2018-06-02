<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 14:20
 */

namespace util\cache;

interface CacheIoManager extends \ArrayAccess
{
    public function write();

    public function purge();

    public function offsetExists($offset);

    public function offsetGet($offset);

    public function offsetSet($offset, $value);

    public function offsetUnset($offset);
}