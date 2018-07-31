<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:16
 */

namespace util\routing;


use handler\RequestHandler;

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
        $this->url      = $url;
        $this->groups   = $groups;
        $this->data     = $data;
        $this->title    = $title;
        $this->abstract = $abstract;
        $this->accepts  = $accepts;
        $this->type     = $type;
    }

    public function getRequestHandler(): RequestHandler
    {
        return $this->handler;
    }

    public function setHandler(RequestHandler $handler): self
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

    public function hasGroup($int): bool
    {
        return in_array($int, $this->groups);
    }

    public function matchesUrl($url): bool
    {
        return preg_match("#^{$this->url}$#", $url);
    }

    public function matchesContentType($accept): bool
    {
        return $this->accepts === $accept;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function jsonSerialize(): array
    {
        return [
            'title'    => $this->title,
            'abstract' => $this->abstract,
            'url'      => $this->url,
            'groups'   => $this->groups,
            'data'     => $this->data,
            'type'     => $this->type,
            'accepts'  => $this->accepts,
        ];
    }

    public function getContentType(): string
    {
        return $this->accepts;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}