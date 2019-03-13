<?php

class Autoloader
{
    public static function loader($className)
    {
        $filename = (DROOT.DIRECTORY_SEPARATOR. str_replace('\\', DIRECTORY_SEPARATOR, $className) . ".php");
        if (file_exists($filename)) {
            include($filename);
            if (class_exists($className)) {
                return true;
            }
        }
        return false;
    }
}
spl_autoload_register('Autoloader::loader');
include DROOT.'/vendor/autoload.php';
