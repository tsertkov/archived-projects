<?php
/**
 * SmartLinks POC
 *
 * This simple POC example shows how to empower website with loading
 * pages content dynamically via AJAX requests but still having
 * working links even when JavaScript is disabled in client browser.
 */

//
// process request
//

foreach (array('mode' => 'full', 'page' => '') as $var => $defValue) {
    $$var = isset($_GET[$var]) ? $_GET[$var] : $defValue;
}

//
// load page data
//

if ('' == $page) {
    $content = 'Loading data...';

} else {
    // filter requested page
    $page = preg_replace('/[^a-z0-9]/i', '', $page);

    @$content = file_get_contents($page . '.phtml');
    if (false === $content) {
        $content = '<strong>Error 404! Page not found</strong>';
    }
}

if ('partial' == $mode) {
    echo $content;
} else {
    require 'layout.phtml';
}
