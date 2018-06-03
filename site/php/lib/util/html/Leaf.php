<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 23:55
 */

namespace util\html;


use container\Collection;

class Leaf extends Base
{
    protected $name;
    protected $tags;

    public function __construct($name)
    {
        $this->name = $name;
        $this->tags = new Collection();
    }

    public function __get($name)
    {
        return $this->tags[$name];
    }

    public function __set($name, $value)
    {
        $this->tags[$name] = $value;
    }

    public function __toString()
    {
        if (!count($this->tags)) {
            return "<{$this->name}>";
        }
        $tags = $this->accumulate();
        return "<{$this->name} $tags>";
    }

    protected function accumulate()
    {
        $tags = '';
        $first = true;
        foreach ($this->tags as $tagname => $tag) {
            if ($first) {
                $first = false;
            } else {
                $tags .= ' ';
            }
            $tags .= "$tagname='$tag'";
        }
        return $tags;
    }


}