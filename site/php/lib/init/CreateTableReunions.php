<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:33
 */

namespace init;


use util\hasWrapper;

class CreateTableReunions extends hasWrapper
{
    public function make()
    {
        echo "Creating Meetings", PHP_EOL;
        $request
            = "CREATE TABLE MEETINGS (meeting_id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, meeting_name VARCHAR(50), user_id BIGINT UNSIGNED NOT NULL REFERENCES USERS (user_id))";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}