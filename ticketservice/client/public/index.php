<?php
/**
 * Front controller
 * @category TicketSellingClient
 * @package Controller
 */

require __DIR__ . '/../bootstrap.php';
use tc\Application;
$application = new Application(APPLICATION_PATH, Application::ENV_DEVELOPMENT);
$application->runClient();