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
    private $title;
    private $abstract;
    private $accepts;
    private $type;

    public function __construct($url, $groups, $data, $title, $abstract, $accepts, $type)
    {
        $this->url = $url;
        $this->groups = $groups;
        $this->data = $data;
        $this->title = $title;
        $this->abstract = $abstract;
        $this->accepts = $accepts;
        $this->type = $type;
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

    public function matchesUrl($url)
    {
        return preg_match("#^{$this->url}$#", $url);
    }

    public function matchesContentType($accept)
    {
        return $this->accepts===$accept;
    }

    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'abstract' => $this->abstract,
            'url' => $this->url,
            'groups' => $this->groups,
            'data' => $this->data,
            'type' => $this->type,
            'accepts' => $this->accepts
        ];
    }

    public function getContentType()
    {
        return $this->accepts;
    }
}