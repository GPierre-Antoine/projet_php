<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 15:56
 */

namespace handler\meeting;


use container\Collection;
use handler\DefaultRanAndSucceed;
use handler\GenericPDORequestHandler;
use handler\HandlerVisitor;
use model\Meeting;
use model\User;

class ListMeetingHandler extends GenericPDORequestHandler
{
    use DefaultRanAndSucceed;

    private $meetings;

    /**
     * @return Collection|Meeting[]
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitListMeeting($this);
    }

    public function run(User $user)
    {
        $this->setRan();
        $stmt = $this->wrapper->run("SELECT user_id, meeting_id, meeting_name FROM MEETINGS WHERE user_id = ?",
            [$user->getId()]);
        $meetings = $stmt->fetchAll()->map(function ($data) {
            return new Meeting($data->meeting_id, $data->user_id, $data->meeting_name);
        });
        $this->meetings = $meetings;
        $this->setSuccess();
    }
}