<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:28
 */

namespace handler;


use handler\connexion\LoginHandler;
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\meeting\AddSlotHandler;
use handler\meeting\CheckMeetingHandler;
use handler\meeting\CheckMeetingVotesHandler;
use handler\meeting\CreateMeetingHandler;
use handler\meeting\DeleteMeetingHandler;
use handler\meeting\ListMeetingHandler;
use handler\meeting\ListSlotHandler;
use handler\meeting\VoteHandler;
use handler\meta\RouteHandler;

interface HandlerVisitor
{
    public function visitLogin(LoginHandler $handler);
    public function visitRegister(RegisterHandler $handler);
    public function visitLogout(LogoutHandler $handler);
    public function visitFakeHandler(FakeHandler $handler);
    public function visitRouteHandler(RouteHandler $handler);
    public function visitListMeeting(ListMeetingHandler $handler);
    public function visitListSlotHandler(ListSlotHandler $handler);
    public function visitCreateMeetingHandler(CreateMeetingHandler $handler);
    public function visitCheckMeetingVotesHandler(CheckMeetingVotesHandler $handler);
    public function visitCheckMeetingHandler(CheckMeetingHandler $handler);
    public function visitAddSlotHandler(AddSlotHandler $handler);
    public function visitVoteHandler(VoteHandler $handler);
    public function visitDeleteMeetingHandler(DeleteMeetingHandler $handler);
}