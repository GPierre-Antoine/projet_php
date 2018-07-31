<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:31
 */

namespace viewer;


use handler\connexion\LoginHandler;
use handler\connexion\LogoutRequestHandler;
use handler\connexion\RegisterHandler;
use handler\FakeRequestHandler;
use handler\RequestHandler;
use handler\meeting\AddSlotHandler;
use handler\meeting\CheckMeetingVotesHandler;
use handler\meeting\CreateMeetingHandler;
use handler\meeting\DeleteMeetingHandler;
use handler\meeting\ListMeetingHandler;
use handler\meeting\VoteHandler;
use handler\meta\RouteRequestHandler;
use util\info\Feedback;

class JsonViewer extends Viewer
{
    public function visitLogin(LoginHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    private function viewBinaryHandler(RequestHandler $handler)
    {
        $message = new Feedback($handler->succeeded(), $handler->succeeded() ? 'Success' : 'Failure');
        echo json_encode($message);
    }

    public function visitRegister(RegisterHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function visitLogout(LogoutRequestHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function visitFakeHandler(FakeRequestHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function getContentType()
    {
        return "application/json";
    }

    public function visitRouteHandler(RouteRequestHandler $handler)
    {
        $feedback = new Feedback($handler->succeeded(), $handler->getRoutes());
        echo json_encode($feedback);
    }

    public function visitCreateMeetingHandler(CreateMeetingHandler $handler)
    {
        $x = new Feedback($handler->succeeded(), $handler->getMeeting());
        echo json_encode($x);
    }

    public function visitAddSlotHandler(AddSlotHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function visitListMeeting(ListMeetingHandler $handler)
    {
        echo json_encode(new Feedback($handler->succeeded(), $handler->getMeetings()));
    }

    public function visitDeleteMeetingHandler(DeleteMeetingHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }


    public function visitCheckMeetingVotesHandler(CheckMeetingVotesHandler $handler)
    {
        echo json_encode(new Feedback($handler->succeeded(), $handler->getMeeting()));
    }


    public function visitVoteHandler(VoteHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }
}