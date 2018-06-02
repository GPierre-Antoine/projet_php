<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 16:52
 */

namespace util;


class Optional
{
    private $value;

    public function __construct($data = null)
    {
        $this->value = $data;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        if (!$this->bool()) {
            throw new \RuntimeException("Invalid Optional");
        }

        return $this->value;
    }

    public function bool()
    {
        return is_null($this->value);
    }
}