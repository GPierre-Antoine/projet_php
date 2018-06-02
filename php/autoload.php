<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 10:15
 *
 * @param string[] $args
 */


function autoload_init_in(string ... $args)
{
    spl_autoload_register(function ($filename) use ($args) {
        $processed_filename = str_replace('\\', DIRECTORY_SEPARATOR, $filename);
        $processed_filename = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR.$processed_filename);
        $errors = [];
        foreach ($args as $base_dir) {
            $final_filename = $base_dir.$processed_filename.'.php';
            if (file_exists($final_filename)) {
                /** @noinspection PhpIncludeInspection */
                require_once $final_filename;

                return;
            }
            $errors[] = $final_filename;
        }
        $data = [];
        $include_if_present = function ($object, $array, $name) {
            if (isset($array[$name])) {
                $object->$name = $array[$name];
            }
        };
        foreach (debug_backtrace() as $value) {
            if (array_key_exists('file', $value)) {
                $object = new \util\MagicalClass();
                $include_if_present($object, $value, 'file');
                $include_if_present($object, $value, 'line');
                $include_if_present($object, $value, 'class');
                $include_if_present($object, $value, 'function');
                $data[] = $object;
            }
        }
        $count = 0;
        $display = array_map(function (\util\MagicalClass $class) use (&$count
        ) {
            $text = PHP_EOL.$class->__toString();
            for ($i = 0; $i < $count; ++$i) {
                $text = str_replace(PHP_EOL, PHP_EOL." ", $text);
            }
            $count += 1;

            return $text;
        }, $data);
        throw new RuntimeException("Bad import : $filename in : ".implode("",
                $display).PHP_EOL);
    });
}