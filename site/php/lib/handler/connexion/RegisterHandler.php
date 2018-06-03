<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:17
 */

namespace handler\connexion;


use handler\DefaultRanAndSucceed;
use handler\GenericPDOHandler;
use handler\HandlerVisitor;
use util\DbWrapper;

class RegisterHandler extends GenericPDOHandler
{
    use DefaultRanAndSucceed;

    const LOGIN = "login";
    const PASSWORD = "password";
    const LASTNAME = "lastname";
    const FIRSTNAME = "firstname";

    public function __construct(DbWrapper $wrapper)
    {
        parent::__construct($wrapper);
    }

    public function run($login, $password, $firstname, $lastname)
    {
        $this->setRan();
        $this->wrapper->run("INSERT INTO USERS (user_firstname, user_lastname) VALUES (?,?)", [$firstname, $lastname]);
        $user_id = $this->wrapper->lastInsertID();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->wrapper->run("INSERT INTO PASSWORDS (password_hash) VALUES (?)", [$hashed_password]);
        $password_id = $this->wrapper->lastInsertID();

        $this->wrapper->run("INSERT INTO LOGINS (login_value) VALUES (?)", [$login]);
        $login_id = $this->wrapper->lastInsertID();

        $this->wrapper->run("INSERT INTO USER_INFO (user_id, password_id, login_id) VALUES ($user_id,$password_id,$login_id)");
        $this->setSuccess();
    }

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitRegister($this);
    }
}