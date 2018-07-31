<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:28
 */

namespace viewer;


use handler\connexion\LoginHandler;
use handler\connexion\LogoutRequestHandler;
use handler\connexion\RegisterHandler;
use handler\FakeRequestHandler;
use handler\HandlerVisitor;
use handler\meeting\AddSlotHandler;
use handler\meeting\CheckMeetingVotesHandler;
use handler\meeting\CreateMeetingHandler;
use handler\meeting\DeleteMeetingHandler;
use handler\meeting\ListMeetingHandler;
use handler\meeting\ListSlotHandler;
use handler\meeting\VoteHandler;
use handler\meta\RouteRequestHandler;

abstract class Viewer implements HandlerVisitor
{
    final public function printContentType()
    {
        header('Content-Type:' . $this->getContentType());
    }

    abstract public function getContentType();

    public function visitLogin(LoginHandler $handler)
    {
        $this->makeException();
    }

    public final function makeException()
    {
        throw new \RuntimeException("Unimplemented Method");
    }

    public function visitRegister(RegisterHandler $handler)
    {
        $this->makeException();
    }

    public function visitLogout(LogoutRequestHandler $handler)
    {
        $this->makeException();
    }

    public function visitFakeHandler(FakeRequestHandler $handler)
    {
        $this->makeException();
    }

    public function visitRouteHandler(RouteRequestHandler $handler)
    {
        $this->makeException();
    }

    public function visitListMeeting(ListMeetingHandler $handler)
    {
        $this->makeException();
    }

    public function visitListSlotHandler(ListSlotHandler $handler)
    {
        $this->makeException();
    }

    public function visitCreateMeetingHandler(CreateMeetingHandler $handler)
    {
        $this->makeException();
    }

    public function visitCheckMeetingVotesHandler(CheckMeetingVotesHandler $handler)
    {
        $this->makeException();
    }

    public function visitAddSlotHandler(AddSlotHandler $handler)
    {
        $this->makeException();
    }

    public function visitVoteHandler(VoteHandler $handler)
    {
        $this->makeException();
    }

    public function visitDeleteMeetingHandler(DeleteMeetingHandler $handler)
    {
        $this->makeException();
    }
}