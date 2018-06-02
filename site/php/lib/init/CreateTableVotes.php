<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:33
 */

namespace init;


use util\hasWrapper;

class CreateTableVotes extends hasWrapper
{

    public function make()
    {
        echo "Creating Votes", PHP_EOL;
        $request
            = "CREATE TABLE MEETING_SLOT_VOTE (meeting_slot_id BIGINT UNSIGNED NOT NULL REFERENCES MEETING_SLOTS (meeting_slot_id), name VARCHAR NOT NULL, PRIMARY KEY meeting_slot_vote_pk (meeting_id, name))";
        $stmt = $this->wrapper->prepare($request);
        $stmt->execute();
    }
}