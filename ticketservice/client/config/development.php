<?php
/**
 * @category TicketSellingClient
 * @package Configuration
 */

return array(
    'restClient' => array(
        'endpoint' => 'http://localhost/server/public/rest/',
        'apiKey' => '056172e185b8045a4e037ff4ce4ae80b',
        'secret' => 'c3f154ee8f4f6735d8f75384825472a8',
        'curlOptions' => array(
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ),
    ),
    'templatesDir' => 'views',
);
