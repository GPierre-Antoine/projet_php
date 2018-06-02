<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 17:00
 */

namespace model;


class User
{
    private $id;
    private $lastname;
    private $firstname;

    public function __construct($id, $lastname, $firstname)
    {
        $this->id = $id;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getLastname()
    {
        return $this->lastname;
    }
}