<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:21
 */

namespace forward;

use container\Collection;
use handler\connexion\LoginHandler;
use handler\FakeHandler;
use handler\meeting\ListMeetingHandler;
use handler\meta\RouteHandler;

class GetForwarder extends Forwarder
{
    private $loginHandler;

    public function __construct(LoginHandler $handler)
    {
        $this->info = new Collection($_GET);
        $this->loginHandler = $handler;
    }

    public function visitFakeHandler(FakeHandler $handler)
    {
        $handler->run();
    }

    public function visitRouteHandler(RouteHandler $handler)
    {
        $handler->run();
    }

    public function visitListMeeting(ListMeetingHandler $handler)
    {
        $handler->run($this->loginHandler->getUser());
    }
}