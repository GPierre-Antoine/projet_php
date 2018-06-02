<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:21
 */

namespace forward;

use container\Collection;

class GetForwarder extends Forwarder
{
    public function __construct()
    {
        $this->info = new Collection($_GET);
    }
}