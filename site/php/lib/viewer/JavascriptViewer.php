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
use handler\meta\RouteHandler;
use util\html\Base;
use util\html\EmulatedBase;
use util\html\HtmlMaker;
use util\html\MultiLeaf;
use util\html\Node;
use util\version\LinuxVersionResolver;

class JavascriptViewer extends Viewer
{

    public function visitLogin(LoginHandler $handler)
    {
        // TODO: Implement visitLogin() method.
    }

    public function visitRegister(RegisterHandler $handler)
    {
        // TODO: Implement visitRegister() method.
    }

    public function visitLogout(LogoutHandler $handler)
    {
        // TODO: Implement visitLogout() method.
    }

    public function visitFakeHandler(FakeHandler $handler)
    {
        // TODO: Implement visitFakeHandler() method.
    }

    public function getContentType()
    {
        return "text/html";
    }

    public function visitRouteHandler(RouteHandler $handler)
    {
        $json = json_encode($handler->getRoutes());
        echo $this->page(new EmulatedBase("<script type='application/javascript'>let app = new MeetingApp(); app.start($json)</script>"));

    }

    public function page(Base ... $bases)
    {

        return new MultiLeaf(
            $this->head(),
            (new Node('BODY'))->append($bases)
        );
    }

    public function head()
    {

        define('site_dir', __DIR__.'/../../..');
        define('js_dir', site_dir.'/js');
        define('css_dir', site_dir.'/css');
        $htmlMaker = new HtmlMaker(new LinuxVersionResolver(site_dir));

        $head = new Node("HEAD");
        $head->append($htmlMaker->vendor());

        $head->append($htmlMaker->resolve(
            js_dir.'/objects.js',
            js_dir.'/factories.js',
            js_dir.'/model.js',
            js_dir.'/log.js',
            js_dir.'/starters.js',
            css_dir.'/alerts.css',
            css_dir.'/basic.css',
            css_dir.'/bs_overwrite.css',
            css_dir.'/theme.css'
        ));

        return $head;
    }
}