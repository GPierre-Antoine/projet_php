<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 17:52
 */

namespace util;


class Settings
{
    private $application_name;
    private $db_host;
    private $db_user;
    private $db_password;

    public function __construct($application_name,
        $db_host,
        $db_user,
        $db_password)
    {
        $this->application_name = $application_name;
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
    }

    /**
     * @return mixed
     */
    public function getApplicationName()
    {
        return $this->application_name;
    }

    /**
     * @return mixed
     */
    public function getDbHost()
    {
        return $this->db_host;
    }

    /**
     * @return mixed
     */
    public function getDbUser()
    {
        return $this->db_user;
    }

    /**
     * @return mixed
     */
    public function getDbPassword()
    {
        return $this->db_password;
    }


}