<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 18:25
 */

namespace model;


class Meeting implements \JsonSerializable
{
    private $id;
    private $user;
    private $name;

    public function __construct($id, $user, $name)
    {
        $this->id = $id;
        $this->user = $user;
        $this->name = $name;
    }

    public function jsonSerialize()
    {
        return ['id'=>$this->id, 'name'=>$this->name, 'user'=>$this->user];
    }
}