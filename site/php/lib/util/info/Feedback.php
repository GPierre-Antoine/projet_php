<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:58
 */

namespace util\info;

use JsonSerializable;

class Feedback implements JsonSerializable
{

    /**
     * @var bool
     */
    private $status;
    /**
     * @var mixed
     */
    private $data;

    public function __construct(bool $status, $data)
    {
        $this->status = $status;
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        return ['status' => $this->status, 'data' => $this->data];
    }
}