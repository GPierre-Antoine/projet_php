<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 16:19
 */

namespace handler\meeting;


use handler\DefaultRanAndSucceed;
use handler\GenericPDOHandler;
use handler\HandlerVisitor;
use model\User;

class DeleteMeetingHandler extends GenericPDOHandler
{
    use DefaultRanAndSucceed;

    const MEETING = 'meeting';

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitDeleteMeetingHandler($this);
    }

    public function run($meeting, User $user)
    {
        $this->setRan();
        $this->wrapper->run("DELETE FROM MEETINGS WHERE meeting_id = ? and user_id = ?",
            [$meeting, $user->getId()]);
        $this->setSuccess();
    }
}