<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:16
 */

namespace util\routing;


use container\Collection;
use handler\Handler;

class RouteFactory
{
    /**
     * @var Collection|Handler[]
     */
    private $handlers;

    public function __construct(Collection $handlers)
    {
        $this->handlers = $handlers;
    }

    public function make($data)
    {
        if (!isset($data->accepts))
            $data->accepts = 'application/json';
        $route = new Route($data->url, $data->groups, $data->data, $data->title, $data->abstract, $data->accepts, $data->type);
        $route->setHandler($this->handlers[$data->handler]);

        return $route;
    }
}