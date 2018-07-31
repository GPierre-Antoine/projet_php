<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 16:19
 */

namespace handler\meeting;


use handler\DefaultRanAndSucceed;
use handler\GenericPDORequestHandler;
use handler\HandlerVisitor;

class AddSlotHandler extends GenericPDORequestHandler
{
    use DefaultRanAndSucceed;

    const MEETING = 'meeting';
    const DATE = 'date';

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitAddSlotHandler($this);
    }

    public function run($meeting, $date)
    {
        $this->setRan();
        $this->wrapper->run("INSERT INTO MEETING_SLOTS (meeting_id, meeting_slot_time, meeting_slot_interval) VALUES (?,?,?)",
            [$meeting, $date, 'PT2H']);
        $this->setSuccess();
    }
}