<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 17:00
 */

namespace model;


class User implements \Serializable, \JsonSerializable
{
    private $id;
    private $lastname;
    private $firstname;
    private $login;
    private $password;

    public function __construct($id, $lastname, $firstname, $login, $password)
    {
        $this->id = $id;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->login = $login;
        $this->password = $password;
    }

    public function serialize()
    {
        return serialize([$this->id, $this->lastname, $this->firstname, $this->login, $this->password]);
    }


    public function unserialize($serialized)
    {
        list($this->id, $this->lastname, $this->firstname, $this->login, $this->password) = unserialize($serialized);
    }

    public function getInfos()
    {
        return new LoginInfo($this->login, $this->password);
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
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


    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'login' => $this->login,
        ];
    }
}