<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 16:19
 */

namespace handler\meeting;


use container\Collection;
use handler\DefaultRanAndSucceed;
use handler\GenericPDOHandler;
use handler\HandlerVisitor;
use model\Meeting;
use model\Slot;
use model\Vote;

class CheckMeetingVotesHandler extends GenericPDOHandler
{
    use DefaultRanAndSucceed;

    private $meeting;

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitCheckMeetingVotesHandler($this);
    }

    public function run($meeting)
    {
        $this->setRan();
        $stmt
            = $this->wrapper->run("SELECT * FROM MEETINGS NATURAL JOIN MEETING_SLOTS WHERE meeting_id = ?",
            [$meeting]);

        /** @var Slot[]|Collection $slots */
        $data = $stmt->useAutoHashMap(function ($data) {
            return $data->meeting_slot_id;
        })->fetchAll();
        $slots = $data->map(function ($data) {
            return new Slot($data->meeting_slot_id, new \DateTime($data->meeting_slot_time),
                new \DateInterval($data->meeting_slot_interval));
        });
        if (!count($slots)) {
            throw new \RuntimeException('Unknown Meeting');
        }
        $mdata = $data->first();
        $meeting = new Meeting($mdata->meeting_id, $mdata->user_id, $mdata->meeting_name);

        foreach ($slots as $slot) {
            $meeting->addSlot($slot);
        }

        $stmt = $this->wrapper->run("SELECT MEETING_SLOT_VOTE.* FROM MEETING_SLOTS JOIN MEETING_SLOT_VOTE ON MEETING_SLOTS.meeting_slot_id = MEETING_SLOT_VOTE.meeting_slot_id
WHERE meeting_id = ?", [$meeting->getId()]);

        $stmt->fetchAll()->map(function ($data) use ($slots) {
            $vote = new Vote($data->vote_id, $data->name);
            $slots[$data->meeting_slot_id]->addVotes($vote);
        });

        $this->meeting = $meeting;
        $this->setSuccess();
    }

    public function getMeeting()
    {
        return $this->meeting;
    }
}