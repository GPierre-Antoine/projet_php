<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 04/06/2018
 * Time: 00:39
 */

namespace model;


class Vote implements \JsonSerializable
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {

        $this->id = $id;
        $this->name = $name;
    }

    public function jsonSerialize()
    {
        return ['id' => $this->id, 'name' => $this->name];
    }
}