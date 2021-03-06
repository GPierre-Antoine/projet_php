<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 16:18
 */

namespace handler\meeting;


use handler\DefaultRanAndSucceed;
use handler\GenericPDORequestHandler;
use handler\HandlerVisitor;

class ListSlotHandler extends GenericPDORequestHandler
{
    use DefaultRanAndSucceed;

    const MEETING = "meeting";

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitListSlotHandler($this);
    }

    public function run($meeting)
    {
        $this->setRan();
        $this->wrapper->run("SELECT * FROM ", [$meeting]);
        $this->setSuccess();
    }
}