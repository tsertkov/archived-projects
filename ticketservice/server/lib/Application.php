<?php
/**
 * @category TicketSellingClient
 * @package Application
 */

namespace ts;
use \Jaer\Application_Abstract;

/**
 * Application
 */
class Application extends Application_Abstract
{
    const ENV_DEVELOPMENT = 'development';

    /**
     * Run ticket selling server application
     */
    public function runServer()
    {
        $server = new Server_Rest($this->getResourceContainer());
        $server->setDefServiceName('Reservation');
        $server->handle();
    }
}