<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 10:14
 */


use container\Collection;
use forward\DefaultForwarder;
use forward\GetForwarder;
use forward\PostForwarder;
use handler\connexion\LoginHandler;
use init\CreateDatabase;
use init\CreateTables;
use util\cache\CacheIoManager;
use util\client\PersistentStore;
use util\DbWrapper;
use util\encryption\AESEncryptionManager;
use util\routing\Route;
use util\Settings;
use viewer\JavascriptViewer;
use viewer\JsonViewer;
use viewer\Viewer;

require_once __DIR__ . '/autoload.php';

autoload_init_in(__DIR__ . '/lib');

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

function get_forwarder($request_type, $loginHandler)
{
    switch ($request_type) {
        case 'POST':
            return new PostForwarder($loginHandler);
        case 'GET':
            return new GetForwarder($loginHandler);
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
    $views               = new Collection();
    $views['javascript'] = new JavascriptViewer($handler);
    $views['json']       = new JsonViewer();

    return $views;
}

function resolve_group($output)
{
    return $output === false ? 0 : 1;
}

function application_meetings(
    Settings $settings,
    CacheIoManager $cache,
    PersistentStore $store
) {
    try {
        $db                =
            new DbWrapper($settings->getApplicationName(), $settings->getDbHost(), $settings->getDbUser(),
                $settings->getDbPassword());
        $encryptionManager = new AESEncryptionManager($cache[AESEncryptionManager::KEY_TYPE]);

        $routeur = new \util\routing\Routeur($db, $store, $cache, $encryptionManager,
            __DIR__ . '/../json/routes.json',
            __DIR__ . '/../json/handlers_map.json'
        );

        $routes = $routeur->getValidRoutes();

        $uri = $_SERVER['REQUEST_URI'];

        if (empty($_SERVER['HTTP_ACCEPT'])) {
            $accept = 'text/html';
        } else {
            $accept = explode(',', explode(';', $_SERVER['HTTP_ACCEPT'])[0])[0];
        }

        $filter = function (Route $item) use ($uri, $accept) {
            return $item->matchesUrl(explode('?', $uri)[0]) && $item->matchesContentType($accept);
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

        $forwarder = get_forwarder($_SERVER['REQUEST_METHOD'], $routeur->getLoginHandler());


        $viewers = init_views($routeur->getLoginHandler());

        $adequate_viewers = $viewers->filter(function (Viewer $v) use ($accept) {
            return $v->getContentType() === $accept;
        });

        if (!count($adequate_viewers)) {
            throw new RuntimeException("Unknown Mime type in HttpAccept header");
        }
        /** @var Viewer $viewer */
        $viewer = $adequate_viewers->first();

        $route->getRequestHandler()->accept($forwarder);
        $viewer->printContentType();
        $route->getRequestHandler()->accept($viewer);

    } catch (Exception $e) {
        header("Content-Type: text/plain");
        try {
            switch ($e->getCode()) {
                case 1049:
                    make_new_folder($cache,
                        $settings->getApplicationName(),
                        $settings->getDbHost(),
                        $settings->getDbUser(),
                        $settings->getDbPassword());
                    break;
                default:
                    http_response_code($e->getCode() || 500);
                    echo $e->getMessage(), PHP_EOL, $e->getTraceAsString();
                    break;
            }
            exit;
        } catch (Exception $e2) {
            echo $e2->getMessage();
        }
    }
}
