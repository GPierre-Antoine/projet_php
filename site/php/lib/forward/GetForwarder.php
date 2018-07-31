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
use handler\FakeRequestHandler;
use handler\meeting\CheckMeetingVotesHandler;
use handler\meeting\ListMeetingHandler;
use handler\meeting\ListSlotHandler;
use handler\meta\RouteHandler;

class GetForwarder extends Forwarder
{
    private $loginHandler;

    public function __construct(LoginHandler $handler)
    {
        $this->info = new Collection($_GET);
        $this->loginHandler = $handler;
    }

    public function visitFakeHandler(FakeRequestHandler $handler)
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

    public function visitListSlotHandler(ListSlotHandler $handler)
    {
        $this->assertHasKey(ListSlotHandler::MEETING);
        $meeting = $this->secureGet(ListSlotHandler::MEETING);
        $handler->run($meeting);
    }

    public function visitCheckMeetingVotesHandler(CheckMeetingVotesHandler $handler)
    {
        $this->assertHasKey(ListSlotHandler::MEETING);
        $meeting = $this->secureGet(ListSlotHandler::MEETING);
        $handler->run($meeting);
    }
}