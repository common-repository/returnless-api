<?php
defined( 'ABSPATH' ) || exit;
function returnLessApiAutoloader($class)
{
    $namespace = 'ReturnlessApi\\';
    if (strpos($class, $namespace) !== 0) {
        return;
    }
    $class = str_replace($namespace, '', $class);
    $filename = RETURN_API_PLUGIN_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($filename)) {
        include_once($filename);
    }
}
spl_autoload_register( 'returnLessApiAutoloader');