<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 02/06/2018
 * Time: 18:28
 */

namespace viewer;


use forward\Forwarder;
use handler\HandlerVisitor;

abstract class Viewer implements HandlerVisitor
{
    final public function printContentType(){
        header('Content-Type:'.$this->getContentType());
    }
    abstract public function getContentType();
}