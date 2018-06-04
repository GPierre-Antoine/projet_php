<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 16:22
 */

namespace handler\meeting;


use handler\DefaultRanAndSucceed;
use handler\GenericPDOHandler;
use handler\HandlerVisitor;

class VoteHandler extends GenericPDOHandler
{
    use DefaultRanAndSucceed;

    const NAME = 'name';
    const SLOT = 'slot';

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitVoteHandler($this);
    }

    public function run($name, $slot)
    {
        $this->setRan();
        $this->wrapper->run("INSERT INTO MEETING_SLOT_VOTE (meeting_slot_id, name) VALUES (?,?)", [$slot, $name]);
        $this->setSuccess();
    }
}