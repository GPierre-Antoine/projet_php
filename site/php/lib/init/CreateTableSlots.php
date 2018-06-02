<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:33
 */

namespace init;


use util\hasWrapper;

class CreateTableSlots extends hasWrapper
{
    public function make()
    {
        echo "Creating Slots", PHP_EOL;
        $request
            = "CREATE TABLE MEETING_SLOTS (meeting_slot_id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, meeting_id BIGINT UNSIGNED NOT NULL REFERENCES MEETINGS (meeting_id), meeting_slot_time TIMESTAMP NOT NULL, meeting_slot_interval VARCHAR(20), CONSTRAINT UNIQUE (meeting_id, meeting_slot_time))";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}