<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:22
 */

namespace handler;


interface Handler
{
    public function hasBeenRan() : bool;
    public function succeeded() : bool;
    public function accept(HandlerVisitor $visitor);
}