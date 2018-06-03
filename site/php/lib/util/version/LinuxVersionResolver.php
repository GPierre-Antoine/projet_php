<?php

namespace util\version;

class LinuxVersionResolver extends VersionResolver
{
    private $site_base;

    public function __construct($site_base)
    {
        $this->site_base = $site_base;
    }


    function resolve($file)
    {
        if (!file_exists($file)) {
            return $file;
        }

        $realpath = ($file);
        $droot    = realpath($this->site_base . '/.');

        $correct_path = substr($realpath, mb_strlen($droot));
        while (true) {
            $new_path = preg_replace('@/[\w-]+/\.\.@', '', $correct_path);
            if ($new_path === $correct_path) {
                break;
            }
            $correct_path = $new_path;

        }
        $final = parent::$virtual_root.$correct_path;
        return parent::cut($final, $file);
    }
}