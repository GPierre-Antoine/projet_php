<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 12:44
 */

namespace init;


use util\hasWrapper;

class CreateDatabase extends hasWrapper
{

    public function make($name)
    {
        $this->wrapper->run("CREATE DATABASE $name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->wrapper->use($name);
    }
}