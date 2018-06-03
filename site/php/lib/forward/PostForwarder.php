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

class PostForwarder extends Forwarder
{
    public function __construct()
    {
        $this->info = new Collection($_POST);
    }

    public function visitLogin(LoginHandler $handler)
    {
        $this->assertHasKey(LoginHandler::LOGIN, LoginHandler::PASSWORD);
        $login = $this->secureGet(LoginHandler::LOGIN);
        $password = $this->unsecureGet(LoginHandler::PASSWORD);
        $handler->run($login, $password);
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
}