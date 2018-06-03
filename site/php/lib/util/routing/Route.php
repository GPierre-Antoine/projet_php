<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:16
 */

namespace util\routing;


use handler\Handler;

class Route implements \JsonSerializable
{
    private $handler;
    private $url;
    private $groups;
    private $data;

    public function __construct($url, $groups, $data)
    {
        $this->url = $url;
        $this->groups = $groups;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param Handler $handler
     *
     * @return Route
     */
    public function setHandler(Handler $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function hasGroup($int)
    {
        return in_array($int, $this->groups);
    }

    public function matchUrl($url)
    {
        return preg_match("#^{$this->url}$#", $url);
    }

    public function jsonSerialize()
    {
        return ['url' => $this->url, 'groups' => $this->groups, 'data' => $this->data];
    }
}