<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:21
 */

namespace forward;

use container\Collection;
use handler\FakeHandler;
use handler\meta\RouteHandler;

class GetForwarder extends Forwarder
{
    public function __construct()
    {
        $this->info = new Collection($_GET);
    }

    public function visitFakeHandler(FakeHandler $handler)
    {
        $handler->run();
    }

    public function visitRouteHandler(RouteHandler $handler)
    {
        $handler->run();
    }
}