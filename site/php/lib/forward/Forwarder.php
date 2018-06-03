<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:20
 */

namespace forward;


use container\Collection;
use handler\connexion\LoginHandler;
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\FakeHandler;
use handler\Handler;
use handler\HandlerVisitor;
use handler\meta\RouteHandler;

class Forwarder implements HandlerVisitor
{
    /** @var Collection */
    protected $info;

    public function visitLogin(LoginHandler $handler)
    {
        $this->makeException();
    }

    public final function makeException()
    {
        throw new \RuntimeException("Unimplemented Method");
    }

    public function visitRegister(RegisterHandler $handler)
    {
        $this->makeException();
    }

    public function assertHasKey(...$string)
    {
        foreach ($string as $value) {
            if (!$this->hasKey($value)) {
                throw new \RuntimeException("Missing argument : " . $value);
            }
        }
    }

    public function hasKey($string)
    {
        return $this->info->hasKey($string) && !empty($this->info[$string]);
    }

    public function secureGet($string)
    {
        return self::secure($this->unsecureGet($string));
    }

    public static function secure($string)
    {
        return htmlentities($string, ENT_QUOTES);
    }

    public function unsecureGet($string)
    {
        return $this->info[$string];
    }

    public function process(Handler $handler)
    {
        $handler->accept($this);
    }

    public function visitLogout(LogoutHandler $handler)
    {
        $this->makeException();
    }

    public function visitFakeHandler(FakeHandler $handler)
    {
        $this->makeException();
    }

    public function visitRouteHandler(RouteHandler $handler)
    {
        $this->makeException();
    }
}