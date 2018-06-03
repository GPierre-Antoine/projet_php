<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 18:25
 */

namespace model;


class Meeting implements \JsonSerializable
{
    private $id;
    private $user;
    private $name;

    private $slots;

    public function __construct($id, $user, $name)
    {
        $this->id = $id;
        $this->user = $user;
        $this->name = $name;
        $this->slots = [];
    }

    /**
     * @param mixed $slot
     */
    public function addSlot(Slot $slot)
    {
        $this->slots[] = $slot;
    }

    public function jsonSerialize()
    {
        return ['id' => $this->id, 'name' => $this->name, 'user' => $this->user, 'slots' => $this->slots];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}