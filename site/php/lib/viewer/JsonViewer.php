<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:31
 */

namespace viewer;


use handler\connexion\LoginHandler;
use handler\connexion\LogoutHandler;
use handler\connexion\RegisterHandler;
use handler\FakeHandler;
use handler\Handler;
use handler\meeting\AddSlotHandler;
use handler\meeting\CreateMeetingHandler;
use handler\meeting\ListMeetingHandler;
use handler\meta\RouteHandler;
use util\info\Feedback;

class JsonViewer extends Viewer
{
    public function visitLogin(LoginHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    private function viewBinaryHandler(Handler $handler)
    {
        $message = new Feedback($handler->succeeded(), $handler->succeeded() ? 'Success' : 'Failure');
        echo json_encode($message);
    }

    public function visitRegister(RegisterHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function visitLogout(LogoutHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function visitFakeHandler(FakeHandler $handler)
    {
        $this->viewBinaryHandler($handler);
    }

    public function getContentType()
    {
        return "application/json";
    }

    public function visitRouteHandler(RouteHandler $handler)
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


}