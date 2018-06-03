<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:27
 */

namespace handler;


class FakeHandler implements Handler
{
    use DefaultRanAndSucceed;

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitFakeHandler($this);
    }

    public function run()
    {
        $this->setSuccess();
        echo "Fake Handler";
    }
}