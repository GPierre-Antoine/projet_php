<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:17
 */

namespace container;


class AutoHashCollection extends Collection
{
    private $id_closure;

    public function __construct($closure, $array = [], $sort_algorithm = null)
    {
        parent::__construct([], $sort_algorithm);
        $this->id_closure = $closure;
        foreach ($array as $value) {
            $this[] = $value;
        }

    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $offset = call_user_func($this->id_closure, $value);
        }
        parent::offsetSet($offset, $value);
    }
}