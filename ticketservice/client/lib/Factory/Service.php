<?php
/**
 * @category TicketSellingClient
 * @package Factory
 */

namespace tc;
use \Jaer\Factory_Abstract;

/**
 * Service factory
 */
class Factory_Service extends Factory_Abstract
{
    protected function _getNamespace()
    {
        // service classes reside in tsSDK package
        return 'tsSDK';
    }

    protected function _createInstance($class)
    {
        return new $class($this->_resourceContainer->getRestClient());
    }

    /**
     * @return Service_Reservation
     */
    public function getReservationService()
    {
        return $this->getInstance('Service_Rest_Reservation');
    }
}