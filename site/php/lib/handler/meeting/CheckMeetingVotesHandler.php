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

class CheckMeetingVotesHandler extends GenericPDOHandler
{
    use DefaultRanAndSucceed;

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitCheckMeetingVotesHandler($this);
    }
}