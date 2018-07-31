<?php
/**
 * Created by PhpStorm.
 * User: pierreantoine
 * Date: 31/07/18
 * Time: 09:38
 */

namespace util\routing;


class HttpRequest
{
    private $uri;
    private $parameters;
    private $http_accept;
    private $request_method;
    /**
     * @var string
     */
    private $method;

    public function __construct(string $uri, array $parameters, string $http_accept, string $method)
    {
        $this->uri         = $uri;
        $this->parameters  = $parameters;
        $this->http_accept = $http_accept;
        $this->method      = $method;
    }

    public static function makeServerArray(array $server)
    {
        list($uri, $parameters) = self::extractUriAndParameters($server['REQUEST_URI']);
        $http_accept = self::extractHttpAccept($server['HTTP_ACCEPT']);
        $method      = self::extractRequestMethod($server['REQUEST_METHOD']);
        return new HttpRequest($uri, $parameters, $http_accept, $method);
    }

    /**
     * @param $uri
     * @return array
     */
    protected static function extractUriAndParameters(string $uri)
    {
        $split_uri  = explode('?', $uri);
        $uri        = $split_uri[0];
        $parameters = null;
        if (!count($split_uri)) {
            $parameters = [];
        }
        else {
            $parameters = $split_uri[1];
        }
        return [$uri, $parameters];
    }

    private static function extractHttpAccept(?string $string): string
    {
        if (is_null($string)) {
            $accept = 'text/html';
        }
        else {
            $type   = explode(';', $string)[0];
            $accept = explode(',', $type)[0];
        }
        return $accept;
    }

    private static function extractRequestMethod($method)
    {
        $method        = mb_strtoupper($method);
        $known_methods = ['GET', 'POST'];
        if (!in_array($method, $known_methods)) {
            return '';
        }
        return $method;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getExpectedMimeType()
    {
        return $this->http_accept;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->request_method;
    }


}