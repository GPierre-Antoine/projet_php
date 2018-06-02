<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 10:32
 */

namespace util;

use stdClass;


class MagicalClass extends stdClass
{
    const CLASSNAME = __CLASS__;

    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;

            return call_user_func_array($func, $args);
        }

        return $this;
    }

    public function __toString()
    {
        $string = '';
        foreach (get_object_vars($this) as $index => $get_object_var) {
            $string .= "$index => $get_object_var\n";
        }

        return $string;
    }
}