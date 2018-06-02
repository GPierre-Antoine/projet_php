<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 17:00
 */

namespace model;


class Contact implements \Serializable
{
    private $user;
    private $info;

    /**
     * Contact constructor.
     *
     * @param User      $user
     * @param LoginInfo $info
     */
    public function __construct(User $user, LoginInfo $info)
    {
        $this->user = $user;
        $this->info = $info;
    }


    public function serialize()
    {
        $array = [$this->user, $this->info];

        return serialize($array);
    }


    public function unserialize($serialized)
    {
        list($this->user, $this->info) = unserialize($serialized);
    }

    public function getUser() : User
    {
        return $this->user;
    }

    public function getLogin() : string
    {
        return $this->info->getLogin();
    }

    public function getPassword() : string
    {
        return $this->info->getPassword();
    }

    public function getInfo() : LoginInfo
    {
        return $this->info;
    }
}