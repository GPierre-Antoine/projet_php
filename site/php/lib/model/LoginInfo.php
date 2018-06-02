<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 17:25
 */

namespace model;


class LoginInfo implements \Serializable
{
    private $login;
    private $password;

    /**
     * LoginInfo constructor.
     *
     * @param $login
     * @param $password
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }


    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }


    public function serialize()
    {
        return serialize([$this->login, $this->password]);
    }


    public function unserialize($serialized)
    {
        list ($this->login, $this->password) = unserialize($serialized);
    }
}