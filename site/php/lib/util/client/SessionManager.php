<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 13:13
 */

namespace util\client;


class SessionManager implements ClientStore
{
    const SERVER_NAME = 'SERVER_NAME';
    const TIMEOUT = 'TIMEOUT';

    private $path;
    private $secure;
    private $duration;

    /**
     * SessionManager constructor.
     *
     * @param $path
     * @param $secure
     * @param $duration
     */
    public function __construct($path = '/', $secure = true, $duration = 3600)
    {
        $this->path = $path;
        $this->secure = $secure;
        $this->duration = $duration;
        $this->start();
    }

    public function start()
    {
        ini_set("session.use_cookies", 1);
        ini_set("session.use_only_cookies", 1);
        ini_set('session.use_trans_sid', 0);
        session_set_cookie_params(0, $this->path, '', $this->secure, true);
        session_start();
    }

    public function destroy()
    {
        setcookie(session_name(), '', time() - 3600, $this->path, '', $this->secure, true);
        $_SESSION = array();
        session_destroy();
    }

    public function offsetExists($offset)
    {
        return isset($_SESSION[self::quickHash($offset)]);
    }

    public static function quickHash($string)
    {
        return 'S'.crc32($string);
    }

    public function offsetGet($offset)
    {
        return $_SESSION[self::quickHash($offset)];
    }

    public function offsetSet($offset, $value)
    {
        $_SESSION[self::quickHash($offset)] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($_SESSION[self::quickHash($offset)]);
    }
}