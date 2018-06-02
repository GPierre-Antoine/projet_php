<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:29
 */

namespace util;

abstract class hasWrapper
{

    /**
     * @var DbWrapper
     */
    protected $wrapper;

    public function __construct(DbWrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }
}