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
     * @var string
     */
    private $message;

    public function __construct(bool $status, string $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    public function jsonSerialize()
    {
        return ['status' => $this->status, 'message' => $this->message];
    }
}