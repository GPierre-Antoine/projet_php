<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 00:01
 */

namespace util\html;


use util\version\VersionResolver;

class HtmlMaker
{

    private $resolver;

    public function __construct(VersionResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function vendor() : Base
    {
        return new MultiLeaf(
            $this->jQueryV3(),
            $this->bootStrapV4()
        );
    }

    public function jQueryV3() : Base
    {
        return
            $this->remoteJs('https://code.jquery.com/jquery-3.3.1.min.js',
                'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=',
                'anonymous');
    }

    public function remoteJs($src, $integrity, $crossorigin) : Base
    {
        $css = $this->js();
        $css->src = $src;
        $css->integrity = $integrity;
        $css->crossorigin = $crossorigin;

        return $css;
    }

    public function js() : Leaf
    {
        $js = new Node("SCRIPT");
        $js->type = "application/javascript";

        return $js;
    }

    public function bootStrapV4() : Base
    {
        return new MultiLeaf(
            $this->remoteCss('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
                'sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm',
                'anonymous'),
            $this->remoteJs('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
                'sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q',
                'anonymous'),
            $this->remoteJs('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
                'sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl',
                'anonymous')
        );
    }

    public function remoteCss($href, $integrity, $crossorigin) : Base
    {
        $css = $this->css();
        $css->href = $href;
        $css->integrity = $integrity;
        $css->crossorigin = $crossorigin;

        return $css;
    }

    public function css() : Leaf
    {
        $css = new Node("LINK");
        $css->rel = "stylesheet";
        $css->type = "text/css";

        return $css;
    }

    public function smartJs($file) : Base
    {
        $js = $this->js();
        $js->src = $this->resolver->resolve($file);

        return $js;
    }

    public function smartCss($file) : Base
    {
        $css = $this->css();
        $css->href = $this->resolver->resolve($file);

        return $css;
    }

    public function resolve(string... $filenames)
    {
        $multicontainer = new MultiLeaf();
        foreach ($filenames as $filename) {
            $leaf = $this->resolve_file($filename);
            if (!is_null($leaf)) {
                $multicontainer->add($leaf);
            }
        }

        return $multicontainer;
    }

    private function resolve_file($filename)
    {
        $server_file = $this->resolver->resolve($filename);
        switch (mb_strip_from_last_index($filename, '.')) {
            default:
                return null;
                break;
            case "js":
                $leaf = $this->js();
                $leaf->src = $server_file;
                break;
            case "css":
                $leaf = $this->css();
                $leaf->href = $server_file;
                break;
        }

        return $leaf;
    }

}