<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 22:29
 */

namespace util\client;


interface ClientStore extends \ArrayAccess
{
    public function start();
    public function destroy();
}