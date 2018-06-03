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
use handler\meta\RouteHandler;
use util\info\CollectionFeedback;
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
        $feedback = new CollectionFeedback($handler->succeeded(), $handler->getRoutes());
        echo json_encode($feedback);
    }
}