<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 19:55
 */

namespace util\info;


class CollectionFeedback
{
    private $data;
    private $status;

    public function __construct(bool $status, $collection)
    {
        $this->status = $status;
        $this->data = $collection;
    }

    public function jsonSerialize()
    {
        return ['status' => $this->status, 'data' => $this->data];
    }
}