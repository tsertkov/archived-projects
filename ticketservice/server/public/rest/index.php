<?php
/**
 * Rest server controller
 * @category TicketSellingService
 * @package Controller
 */

require __DIR__ . '/../../bootstrap.php';
use ts\Application;
$application = new Application(APPLICATION_PATH, Application::ENV_DEVELOPMENT);
$application->runServer();