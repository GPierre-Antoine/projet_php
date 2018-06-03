<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 17:08
 */

namespace handler\connexion;


use handler\DefaultRanAndSucceed;
use handler\Handler;
use handler\HandlerVisitor;
use util\cache\CacheIoManager;
use util\client\ClientStore;

class LogoutHandler implements Handler
{
    use DefaultRanAndSucceed;
    private $clientStore;
    private $cacheIoManager;

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitLogout($this);
    }

    public function run()
    {
        $this->setRan();
        unset($this->clientStore[LoginHandler::LOGIN_INFO]);
        $this->clientStore->destroy();
        $this->setSuccess();
    }

    public function __construct(
        ClientStore $clientStore,
        CacheIoManager $cacheIoManager
    ) {
        $this->clientStore = $clientStore;
        $this->cacheIoManager = $cacheIoManager;
    }
}