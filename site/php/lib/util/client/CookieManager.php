<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 16:09
 */

namespace util\client;


use container\CollectionForwarder;

class CookieManager extends CollectionForwarder implements PersistentStore
{
    protected $secure;
    protected $default_expiration_time;

    public function __construct($default_expiration_time, $secure)
    {
        parent::__construct($_COOKIE);
        $this->default_expiration_time = $default_expiration_time;
        $this->secure = $secure;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \RuntimeException("No name set for cookie");
        }
        $this->makeCookie($offset, $value, $this->default_expiration_time, $this->secure);
    }

    public function makeCookie($offset, $value, $expiration_time, $secure)
    {
        $offset = self::quickHash($offset);
        setcookie($offset, $value, time() + $expiration_time, "", "", $secure, true);
        $this->collection->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->deleteCookie($offset);
    }

    public function deleteCookie($offset)
    {
        $offset = self::quickHash($offset);
        $this->deleteCookieRaw($offset);
    }


    private function deleteCookieRaw($offset)
    {
        setcookie($offset, '', time() - 3600);
        $this->collection->offsetUnset($offset);
    }


    public function destroy()
    {
        $names = $this->collection->keys();
        foreach ($names as $cookie) {
            $this->deleteCookieRaw($cookie);
        }
    }

    public function start()
    {
    }
}