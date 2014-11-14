<?php
/**
 * @category TicketSellingService
 * @package Controller
 */

$restEndpoint = 'http://' . $_SERVER['HTTP_HOST']
  . str_replace(array('\\', '//'), '/', dirname($_SERVER['SCRIPT_NAME']) . '/rest/');

$restMethods = array(
    'getFreeSeatsNum' => array('GET/POST', array()),
    'getFreeSeatsList' => array('GET/POST', array('int $number')),
    'reserveSeats' => array('POST', array('string $name', 'array $seats')),
);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Tiket selling service</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
  </head>
  <body>
    <h1>Ticket selling service</h1>
    <p>Rest request format: <em><?php echo $restEndpoint ?>?method=methodName&amp;name=value</em></p>
    <p>
      <strong>Required arguments:</strong>
      <ul>
        <li>api_key - client api key</li>
        <li>api_sig - request signature</li>
      </ul>
    </p>
    <p>
      <strong>Available methods:</strong>
      <ul>
      <?php foreach ($restMethods as $method => $meta): ?>
        <li><?php printf('%s: %s?%s(%s)', $meta[0], $restEndpoint, $method, implode(', ', $meta[1])); ?></li>
      <?php endforeach; ?>
      </ul>
    </p>
  </body>
</html>