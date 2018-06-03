<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 19:49
 */

namespace handler\meta;


use container\Collection;
use handler\DefaultRanAndSucceed;
use handler\Handler;
use handler\HandlerVisitor;

class RouteHandler implements Handler
{
    use DefaultRanAndSucceed;

    private $routes;


    /**
     * @param mixed $routes
     */
    public function setRoutes(Collection $routes)
    {
        $this->routes = $routes;
    }

    public function __construct()
    {
    }

    /**
     * @return Collection
     */
    public function getRoutes() : Collection
    {
        return $this->routes;
    }

    public function run()
    {
        $this->setSuccess();
    }

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitRouteHandler($this);
    }
}