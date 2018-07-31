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
use model\Meeting;
use model\User;

class CreateMeetingHandler extends GenericPDORequestHandler
{
    use DefaultRanAndSucceed;

    const NAME = 'name';
    private $meeting;

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitCreateMeetingHandler($this);
    }


    public function run(User $user, $name)
    {
        $this->setRan();
        $this->wrapper->run("INSERT INTO MEETINGS (user_id,meeting_name) VALUES (?,?)", [$user->getId(), $name]);
        $this->meeting = new Meeting($this->wrapper->lastInsertID(), $user, $name);
        $this->setSuccess();
    }

    public function getMeeting() : Meeting
    {
        return $this->meeting;
    }
}