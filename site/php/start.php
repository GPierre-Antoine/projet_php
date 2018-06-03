<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 10:14
 */


use container\AutoHashCollection;
use container\Collection;
use forward\DefaultForwarder;
use forward\GetForwarder;
use forward\PostForwarder;
use handler\connexion\LoginHandler;
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\FakeHandler;
use handler\Handler;
use handler\meta\RouteHandler;
use init\CreateDatabase;
use init\CreateTables;
use util\cache\CacheIoManager;
use util\client\ClientStore;
use util\DbWrapper;
use util\encryption\AESEncryptionManager;
use util\encryption\EncryptionManager;
use util\routing\Route;
use util\routing\RouteFactory;
use util\Settings;
use viewer\JavascriptViewer;
use viewer\JsonViewer;
use viewer\Viewer;

require_once __DIR__.'/autoload.php';

autoload_init_in(__DIR__.'/lib');

/**
 * @param CacheIoManager $cache
 * @param                $application_name
 * @param                $db_host
 * @param                $db_user
 * @param                $db_password
 *
 * @throws Exception
 */
function make_new_folder(CacheIoManager $cache, $application_name, $db_host, $db_user, $db_password)
{
    handle_database_creation($application_name, $db_host, $db_user, $db_password);
    $cache[AESEncryptionManager::KEY_TYPE] = AESEncryptionManager::makeHexKey();
}

function handle_database_creation($db_name, $db_host, $db_user, $db_password)
{
    echo "Initializating folder : ", $db_name, PHP_EOL;
    $db = new DbWrapper('', $db_host, $db_user, $db_password);
    try {
        $db->beginTransaction();
        $db_creator = new CreateDatabase($db);
        $db_creator->make($db_name);
        $tables_creator = new CreateTables($db);
        $tables_creator->run();
        $db->endTransaction();
    } catch (Exception $e) {
        $db->rollback();
        echo "An error occured : ", $e->getMessage();
    }
}

function mb_strip_from_last_index($string, $needle)
{
    $last_index = mb_strrpos($string, $needle) + 1;

    return mb_substr($string, $last_index);
}

function get_forwarder($request_type)
{
    switch ($request_type) {
        case 'POST':
            return new PostForwarder();
        case 'GET':
            return new GetForwarder();
        default:
            return new DefaultForwarder();
    }

}

/**
 * @param LoginHandler $handler
 *
 * @return Collection|Viewer[]
 */
function init_views(LoginHandler $handler)
{
    $views = new Collection();
    $views['javascript'] = new JavascriptViewer($handler);
    $views['json'] = new JsonViewer();

    return $views;
}

/**
 * @param DbWrapper         $db
 * @param CacheIoManager    $cache
 * @param ClientStore       $store
 * @param EncryptionManager $encryptionManager
 *
 * @return Collection|Handler[]
 */
function init_handlers(
    DbWrapper $db,
    CacheIoManager $cache,
    ClientStore $store,
    EncryptionManager $encryptionManager
) {
    $handlers_file = __DIR__.'/../json/handlers_map.json';
    $keys = json_decode(file_get_contents($handlers_file), true);
    $hash_f = function (Handler $h) use ($keys) {
        return $keys[mb_strip_from_last_index(get_class($h), '\\')];
    };
    $handlers = new AutoHashCollection($hash_f);
    $handlers[] = new RegisterHandler($db);
    $handlers[] = new LogoutHandler($store, $cache);
    $handlers[] = new FakeHandler();
    $handlers[] = new RouteHandler();

    return $handlers;
}

function get_routes($db, $store, $cache, $encryptionManager)
{
    $login_handler = new LoginHandler($db, $store, $cache, $encryptionManager);


    $handlers = init_handlers($db, $cache, $store, $encryptionManager);
    $handlers[] = $login_handler;

    $make_function = [new RouteFactory($handlers), 'make'];

    $routes = json_decode(file_get_contents(__DIR__.'/../json/routes.json'));

    /** @var Collection|Route[] $collection */
    $collection = new Collection(array_map($make_function, (array)$routes));

    $result = $login_handler->attemptCacheLogin();
    $group = resolve_group($result);

    $filtererd_collection = $collection->filter(function (Route $item) use ($group) {
        return $item->hasGroup($group);
    });

    $handlers['routes']->setRoutes($collection);

    return $filtererd_collection;
}

function resolve_group($output)
{
    return $output === false ? 0 : 1;
}

function application_meetings(
    Settings $settings,
    CacheIoManager $cache,
    ClientStore $store
) {
    try {
        $store->start();
        $db = new DbWrapper($settings->getApplicationName(), $settings->getDbHost(), $settings->getDbUser(),
            $settings->getDbPassword());
        $encryptionManager = new AESEncryptionManager($cache[AESEncryptionManager::KEY_TYPE]);

        $routes = get_routes($db, $store, $cache, $encryptionManager);
        $uri = $_SERVER['REQUEST_URI'];

        if (empty($_SERVER['HTTP_ACCEPT'])) {
            $accept = 'text/html';
        } else {
            $accept = explode(',', explode(';', $_SERVER['HTTP_ACCEPT'])[0])[0];
        }

        $filter = function (Route $item) use ($uri, $accept) {
            return $item->matchesUrl($uri) && $item->matchesContentType($accept);
        };
        $right_handler = $routes->filter($filter);

        if (!count($right_handler)) {
            throw new RuntimeException("Unknown route : ".$uri);
        }

        if (count($right_handler) > 1) {
            throw new RuntimeException("Ambiguous route : ".$uri);
        }

        /** @var Route $route */
        $route = $right_handler->first();

        $forwarder = get_forwarder($_SERVER['REQUEST_METHOD']);


        $viewers = init_views($routes['login']->getHandler());

        $adequate_viewers = $viewers->filter(function (Viewer $v) use ($accept) {
            return $v->getContentType() === $accept;
        });

        if (!count($adequate_viewers)) {
            throw new RuntimeException("Bad request");
        }
        /** @var Viewer $viewer */
        $viewer = $adequate_viewers->first();

        $route->getHandler()->accept($forwarder);
        $route->getHandler()->accept($viewer);

    } catch (Exception $e) {
        header("Content-Type: text/plain");
        try {
            switch ($e->getCode()) {
                case 1049:
                    make_new_folder($cache, $settings->getApplicationName(), $settings->getDbHost(),
                        $settings->getDbUser(), $settings->getDbPassword());
                    break;
                default:
                    http_response_code($e->getCode() || 500);
                    echo $e->getMessage();
                    break;
            }
            exit;
        } catch (Exception $e2) {
            echo $e2->getMessage();
        }
    }
}