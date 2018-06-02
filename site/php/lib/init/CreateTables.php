<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:24
 */

namespace init;


use container\Collection;
use util\hasWrapper;

class CreateTables extends hasWrapper
{

    public function run()
    {
        $creators = new Collection();
        $creators[] = new CreateTableUsers($this->wrapper);
        $creators[] = new CreateTableLogins($this->wrapper);
        $creators[] = new CreateTablePasswords($this->wrapper);
        $creators[] = new CreateTableUserInfo($this->wrapper);
        $creators[] = new CreateTableReunions($this->wrapper);
        $creators[] = new CreateTableSlots($this->wrapper);
        $creators[] = new CreateTableVotes($this->wrapper);

        foreach ($creators as $creator) {
            $creator->make();
        }
    }
}