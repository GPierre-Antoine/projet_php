<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:28
 */

namespace init;


use util\hasWrapper;

class CreateTableUsers extends hasWrapper
{
    public function make()
    {
        echo "Creating Users", PHP_EOL;
        $request
            = "CREATE TABLE USERS ("
            ."user_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, "
            ."user_firstname VARCHAR(50), "
            ."user_lastname  VARCHAR(80)"
            .")";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}