<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:28
 */

namespace handler;


use handler\connexion\LoginHandler;
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\meta\RouteHandler;

interface HandlerVisitor
{
    public function visitLogin(LoginHandler $handler);

    public function visitRegister(RegisterHandler $handler);

    public function visitLogout(LogoutHandler $handler);

    public function visitFakeHandler(FakeHandler $handler);

    public function visitRouteHandler(RouteHandler $handler);
}