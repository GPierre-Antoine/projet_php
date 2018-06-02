<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:33
 */

namespace init;


use util\hasWrapper;

class CreateTablePasswords extends hasWrapper
{

    public function make()
    {
        echo "Creating Password", PHP_EOL;
        $request
            = "CREATE TABLE PASSWORDS ("
            ."password_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, "
            ."password_hash VARCHAR(100)"
            .")";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}