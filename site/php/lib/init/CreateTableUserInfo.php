<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:33
 */

namespace init;


use util\hasWrapper;

class CreateTableUserInfo extends hasWrapper
{
    public function make()
    {
        echo "Creating user infos", PHP_EOL;
        $request
            = "CREATE TABLE USER_INFO ("
            ."user_id BIGINT UNSIGNED NOT NULL REFERENCES USERS(user_id),"
            ."info_start_validity TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,"
            ."info_end_validity TIMESTAMP NULL,"
            ."password_id BIGINT UNSIGNED NOT NULL REFERENCES PASSWORDS(password_id),"
            ."login_id BIGINT UNSIGNED NOT NULL REFERENCES LOGINS(login_id),"
            ."PRIMARY KEY user_info_pk (user_id, info_start_validity)"
            .")";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}