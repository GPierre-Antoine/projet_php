<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:51
 */

namespace handler;


trait DefaultRanAndSucceed
{
    private $ran = false;
    private $success = false;

    public function succeeded() : bool
    {
        if (!$this->ran)
            throw new \RuntimeException("Handler not ran");
        return $this->success;
    }

    public function hasBeenRan() : bool
    {
        return $this->ran;
    }

    public function setRan()
    {
        $this->ran = true;
    }

    public function setSuccess()
    {
        if (!$this->ran) {
            $this->ran = true;
        }
        $this->success = true;
    }
}