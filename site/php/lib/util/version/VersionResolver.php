<?php

namespace util\version;

abstract class VersionResolver
{
    public static $virtual_root = '';
    abstract function resolve($file);
    public static function cut ($string,$file){
        if (strpos($string, 'static'))
        {
            $last_dot = mb_strrpos($string, '.');
            $f1 = mb_substr($string, 0,$last_dot);
            $f2 = mb_substr($string, $last_dot);
            $string = $f1 . '.version-' . filemtime($file) . $f2;
        }
        return $string;
    }
}