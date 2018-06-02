<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 16:04
 */

namespace util\encryption;

abstract class EncryptionManager
{

    const DEFAULT_KEY_LENGTH = 16;
    const IV_NAME = 'IV';

    /**
     * @param int $key_length
     *
     * @return string
     * @throws \Exception
     */
    public static function makeHexKey($key_length = self::DEFAULT_KEY_LENGTH)
    {
        return bin2hex(self::makeBinaryKey($key_length));
    }

    /**
     * @param int $key_length
     *
     * @return string
     * @throws \Exception
     */
    public static function makeBinaryKey($key_length = self::DEFAULT_KEY_LENGTH)
    {
        return random_bytes($key_length);
    }

    /**
     * @param int $key_length
     *
     * @return float|int
     * @throws \Exception
     */
    public static function makeNumericKey($key_length = self::DEFAULT_KEY_LENGTH)
    {
        return self::makeBinaryKey($key_length);
    }

    abstract public function encrypt($string);

    abstract public function decrypt($string);

    /**
     * @return string
     */
    abstract public function getIv();

    /**
     * @param string $iv
     *
     */
    abstract public function setIv($iv);
}