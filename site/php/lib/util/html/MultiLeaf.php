<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 00:02
 */

namespace util\html;


use container\Collection;

class MultiLeaf extends Base
{
    private $content;

    public function __construct(... $leaves)
    {
        $this->content = new Collection();
        $this->add($leaves);
    }

    public function add(... $leaves)
    {
        foreach ($leaves as $leaf) {
            if (is_array($leaf)) {
                $this->add(...$leaf);
            } else {
                $this->addBase($leaf);
            }
        }
    }

    public function addBase(Base $base)
    {
        $this->content[] = $base;
    }

    public function __toString()
    {
        return $this->content->map(function (Base $item) {
            return $item->__toString();
        })->join('');
    }
}