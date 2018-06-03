<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:31
 */

namespace viewer;


use handler\connexion\LoginHandler;
use handler\meta\RouteHandler;
use util\html\Base;
use util\html\EmulatedBase;
use util\html\HtmlMaker;
use util\html\MultiLeaf;
use util\html\Node;
use util\version\LinuxVersionResolver;

class JavascriptViewer extends Viewer
{
    /**
     * @var LoginHandler
     */
    private $loginHandler;

    public function __construct(LoginHandler $loginHandler)
    {
        $this->loginHandler = $loginHandler;
    }

    public function getContentType()
    {
        return "text/html";
    }

    public function visitRouteHandler(RouteHandler $handler)
    {
        $json = json_encode($handler->getRoutes(), JSON_PRETTY_PRINT);
        $types = file_get_contents(__DIR__.'/../../../json/types.json');
        $identity = $this->loginHandler->attemptCacheLogin();
        $group = resolve_group($identity);
        if ($group>0){
            $user = ",user:".json_encode($identity);
        }
        else{
            $user='';
        }
        echo $this->makePage(new EmulatedBase("<script type='application/javascript'>let app = new MeetingApp(); app.start({routes:$json,group:$group,types:$types{$user}})</script>"));

    }

    public function makePage(Base ... $bases)
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
            css_dir.'/bs_overwrite.css',
            js_dir.'/event.js',
            js_dir.'/functions.js',
            js_dir.'/objects.js',
            js_dir.'/factories.js',
            js_dir.'/model.js',
            js_dir.'/log.js',
            js_dir.'/routes.js',
            js_dir.'/activity/Activity.js',
            js_dir.'/activity/LoginActivity.js',
            js_dir.'/activity/LogoutActivity.js',
            js_dir.'/activity/RegisterActivity.js',
            js_dir.'/activity/CreateMeetingActivity.js',
            js_dir.'/activity/RouteActivity.js',
            js_dir.'/starters.js',
            css_dir.'/alerts.css',
            css_dir.'/basic.css',

            css_dir.'/theme.css'
        ));

        return $head;
    }
}