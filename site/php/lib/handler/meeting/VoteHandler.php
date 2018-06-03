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

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitVoteHandler($this);
    }
}