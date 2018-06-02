<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:30
 */

namespace init;


use util\hasWrapper;

class CreateTableLogins extends hasWrapper
{

    public function make()
    {
        echo "Creating Logins", PHP_EOL;
        $request
            = "CREATE TABLE LOGINS (login_id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, login_value VARCHAR(50))";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}