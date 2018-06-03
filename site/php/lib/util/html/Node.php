<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 23:54
 */

namespace util\html;


class Node extends Leaf
{
    /**
     * @var MultiLeaf
     */
    private $holder;

    public function append(... $items)
    {
        if (is_null($this->holder)) {
            $this->holder = new MultiLeaf($items);
        } else {
            $this->holder->add($items);
        }
        return $this;
    }

    public function __toString()
    {
        $holder = is_null($this->holder) ? '' : $this->holder->__toString();
        return parent::__toString()."$holder</{$this->name}>";
    }
}