<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 14:32
 */

namespace util\encryption;


class AESEncryptionManager extends EncryptionManager
{
    const KEY_TYPE = "AES-256-CBC";

    private $pass;
    private $iv;

    /**
     * EncryptionManager constructor.
     *
     * @param $pass
     */
    public function __construct($pass)
    {
        $this->pass = $pass;
    }

    public function encrypt($string)
    {
        return openssl_encrypt($string, self::KEY_TYPE, $this->pass, 0, $this->iv);
    }

    public function decrypt($string)
    {
        return openssl_decrypt($string, self::KEY_TYPE, $this->pass, 0, $this->iv);
    }

    /**
     * @return mixed
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @param string $iv
     *
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }
}