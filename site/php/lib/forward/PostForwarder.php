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
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\meeting\AddSlotHandler;
use handler\meeting\CreateMeetingHandler;
use handler\meeting\DeleteMeetingHandler;

class PostForwarder extends Forwarder
{
    private $loginHandler;

    public function __construct(LoginHandler $handler)
    {
        $this->info = new Collection($_POST);
        $this->loginHandler = $handler;
    }

    public function visitLogin(LoginHandler $handler)
    {
        $this->assertHasKey(LoginHandler::LOGIN, LoginHandler::PASSWORD);
        $login = $this->secureGet(LoginHandler::LOGIN);
        $password = $this->unsecureGet(LoginHandler::PASSWORD);
        try {
            $handler->run($login, $password);
        } catch (\Exception $e) {
        }
    }

    public function visitRegister(RegisterHandler $handler)
    {
        $this->assertHasKey(RegisterHandler::LOGIN, RegisterHandler::PASSWORD, RegisterHandler::FIRSTNAME,
            RegisterHandler::LASTNAME);

        $login = $this->secureGet(RegisterHandler::LOGIN);
        $lastname = $this->secureGet(RegisterHandler::LASTNAME);
        $firstname = $this->secureGet(RegisterHandler::FIRSTNAME);

        $password = $this->unsecureGet(RegisterHandler::PASSWORD);

        $handler->run($login, $password, $firstname, $lastname);
    }

    public function visitLogout(LogoutHandler $handler)
    {
        $handler->run();
    }

    public function visitCreateMeetingHandler(CreateMeetingHandler $handler)
    {
        $this->assertHasKey(CreateMeetingHandler::NAME);
        $name = $this->secureGet(CreateMeetingHandler::NAME);
        $handler->run($this->loginHandler->getUser(), $name);
    }

    public function visitAddSlotHandler(AddSlotHandler $handler)
    {
        $this->assertHasKey(AddSlotHandler::MEETING, AddSlotHandler::DATE);
        $meeting = $this->secureGet(AddSlotHandler::MEETING);
        $date = $this->secureGet(AddSlotHandler::DATE);
        $handler->run($meeting, $date);
    }

    public function visitDeleteMeetingHandler(DeleteMeetingHandler $handler)
    {
        $this->assertHasKey(AddSlotHandler::MEETING);
        $meeting = $this->secureGet(AddSlotHandler::MEETING);
        $handler->run($meeting, $this->loginHandler->getUser());
    }
}