<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 23:17
 */

namespace model;


class LoginInfo implements \Serializable
{
    private $login;
    private $password;

    public function __construct($login, $password)
    {

        $this->login = $login;
        $this->password = $password;
    }

    public function serialize()
    {
        return serialize([$this->login, $this->password]);
    }


    public function unserialize($serialized)
    {
        list ($this->login, $this->password) = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }


}