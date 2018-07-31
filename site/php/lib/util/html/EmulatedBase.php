<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 03/06/2018
 * Time: 00:43
 */

namespace util\html;


class EmulatedBase extends Base
{
    private $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}