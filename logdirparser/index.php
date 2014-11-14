<?php
/**
 * LogDirParser
 * @package LogDirParser
 */

//
// bootstrap logic
//

set_include_path('.' . PATH_SEPARATOR . dirname(__FILE__) . '/lib/');

/**
 * Custom class autoloading
 * @param string $class
 */
function __autoload($class)
{
    $file = str_replace('_', '/', $class) . '.php';
    require_once $file;
}

//
// controller logic
//

$dirParser = new LogDirParser(dirname(__FILE__) . '/data/');

//
// view logic
//

ob_start();
include 'views/paymentLog.phtml';
$content = ob_get_contents();
ob_end_clean();
include 'views/layout.phtml';
