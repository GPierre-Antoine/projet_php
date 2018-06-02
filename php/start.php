<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 10:14
 */


use init\CreateDatabase;
use init\CreateTables;
use util\cache\CacheIoManager;
use util\DbWrapper;
use util\encryption\AESEncryptionManager;

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

function application_meetings($application_name, $db_host, $db_user, $db_password, CacheIoManager $cache)
{
    try {
        $db = new DbWrapper($application_name, $db_host, $db_user, $db_password);
        $encryptionManager = new AESEncryptionManager($cache[AESEncryptionManager::KEY_TYPE]);

    } catch (Exception $e) {
        header("Content-Type: text/plain");
        try {
            switch ($e->getCode()) {
                case 1049:
                    make_new_folder($cache, $application_name, $db_host, $db_user, $db_password);
                    break;
                default:
                    echo $e->getMessage(), PHP_EOL, $e->getCode(), PHP_EOL, $e->getTraceAsString();
                    break;
            }
            exit;
        } catch (Exception $e2) {
            echo $e2->getMessage();
        }
    }
}