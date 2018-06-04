<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 04/06/2018
 * Time: 00:39
 */

namespace model;


class Slot implements \JsonSerializable
{
    private $id;
    /**
     * @var \DateTime
     */
    private $date;
    private $interval;
    private $votes;

    public function __construct($id, $date, $interval)
    {
        $this->id = $id;
        $this->date = $date;
        $this->interval = $interval;
        $this->votes = [];
    }


    /**
     * @param Vote $votes
     */
    public function addVotes(Vote $votes)
    {
        $this->votes = $votes;
    }

    public function jsonSerialize()
    {
        return ['id' => $this->id, 'time' => $this->date->getTimestamp(), 'votes' => $this->votes];
    }
}