<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 13:13
 */

namespace util;


use container\Collection;
use container\CollectionForwarder;
use util\client\ClientStore;

class SessionManager extends CollectionForwarder implements ClientStore
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
    public function __construct($path, $secure, $duration)
    {
        parent::__construct([]);
        $this->path = $path;
        $this->secure = $secure;
        $this->duration = $duration;
    }

    public function start()
    {
        ini_set("session.use_cookies", 1);
        ini_set("session.use_only_cookies", 1);
        ini_set('session.use_trans_sid', 0);
        session_set_cookie_params(0, $this->path, '', $this->secure, true);
        session_start();
        $this->collection = new Collection($_SESSION);
    }

    public function destroy()
    {
        setcookie(session_name(), '', time() - 3600, $this->path, '', $this->secure, true);
        $_SESSION = array();
        session_destroy();
    }
}