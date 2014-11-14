<?php
/**
 * @category TicketSellingClient
 * @package Application
 */

namespace tc;
use \Jaer\Application_Abstract;

/**
 * Application
 */
class Application extends Application_Abstract
{
    const ENV_DEVELOPMENT = 'development';

    /**
     * Run ticket selling client application
     */
    public function runClient()
    {
        new Controller($this->getResourceContainer());
    }
}