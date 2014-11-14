<?php
/**
 * Static bootstrap script
 * @category TicketSellingClient
 * @package Bootstrap
 */

error_reporting(E_ALL | E_STRICT);
define('APPLICATION_PATH', __DIR__);
set_include_path('.' . PATH_SEPARATOR . realpath(APPLICATION_PATH . '/../lib'));

// custom class autoloading with namespaces support
spl_autoload_register(function($class){
    if (!strncmp($class, 'tc\\', 3)) {
        $class = substr($class, 3);
        $file = APPLICATION_PATH . '/lib/' . str_replace('_', '/', $class) . '.php';

    } else {
        $file = str_replace(array('_', '\\'), '/', $class) . '.php';
    }

    require $file;
});
