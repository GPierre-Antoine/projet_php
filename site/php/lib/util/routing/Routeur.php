<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 15:37
 */

namespace util\routing;


use container\AutoHashCollection;
use container\Collection;
use handler\connexion\LoginHandler;
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\Handler;
use handler\meeting\AddSlotHandler;
use handler\meeting\CheckMeetingVotesHandler;
use handler\meeting\CreateMeetingHandler;
use handler\meeting\DeleteMeetingHandler;
use handler\meeting\ListMeetingHandler;
use handler\meeting\ListSlotHandler;
use handler\meeting\VoteHandler;
use handler\meta\RouteHandler;

class Routeur
{

    private $loginHandler;
    private $routeHandler;
    private $db;
    private $store;
    private $cache;
    private $encryptionManager;
    private $map;
    private $handlers_map;

    public function __construct($db, $store, $cache, $encryptionManager, $map, $handlers_map)
    {
        $this->db = $db;
        $this->store = $store;
        $this->cache = $cache;
        $this->encryptionManager = $encryptionManager;
        $this->loginHandler = new LoginHandler($db, $store, $cache, $encryptionManager);
        $this->routeHandler = new RouteHandler();
        $this->map = $map;
        $this->handlers_map = $handlers_map;
    }

    public function getValidRoutes()
    {
        $handlers = $this->makeHandlers();
        $make_function = [new RouteFactory($handlers), 'make'];

        $routes = json_decode(file_get_contents($this->map));

        /** @var Collection|Route[] $collection */
        $collection = new Collection(array_map($make_function, (array) $routes));

        $handlers['routes']->setRoutes($collection);
        $result = $this->loginHandler->attemptCacheLogin();
        $group = resolve_group($result);

        $filtererd_collection = $collection->filter(function (Route $item) use ($group) {
            return $item->hasGroup($group);
        });

        return $filtererd_collection;
    }

    public function makeHandlers()
    {
        $keys = json_decode(file_get_contents($this->handlers_map), true);
        $hash_f = function (Handler $h) use ($keys) {
            return $keys[mb_strip_from_last_index(get_class($h), '\\')];
        };

        $handlers = new AutoHashCollection($hash_f);

        $handlers[] = $this->routeHandler;
        $handlers[] = $this->loginHandler;
        $handlers[] = new VoteHandler($this->db);
        $handlers[] = new AddSlotHandler($this->db);
        $handlers[] = new ListSlotHandler($this->db);
        $handlers[] = new RegisterHandler($this->db);
        $handlers[] = new ListMeetingHandler($this->db);
        $handlers[] = new CreateMeetingHandler($this->db);
        $handlers[] = new DeleteMeetingHandler($this->db);
        $handlers[] = new CheckMeetingVotesHandler($this->db);
        $handlers[] = new LogoutHandler($this->store, $this->cache);

        return $handlers;
    }

    /**
     * @return LoginHandler
     */
    public function getLoginHandler() : LoginHandler
    {
        return $this->loginHandler;
    }

    /**
     * @param LoginHandler $loginHandler
     */
    public function setLoginHandler(LoginHandler $loginHandler) : void
    {
        $this->loginHandler = $loginHandler;
    }
}