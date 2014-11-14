<?php
/**
 * @category TicketSellingService
 * @package Factory
 */

namespace ts;
use \Jaer\Factory_Abstract;

/**
 * Service factory
 */
class Factory_Service extends Factory_Abstract
{
    /**
     * @var int
     */
    protected $_clientId;

    /**
     * @param int $clientId
     */
    public function setClientId($clientId)
    {
        $this->_clientId = $clientId;
    }

    protected function _createInstance($class)
    {
        if (null === $this->_clientId) {
            $msg = 'Client id should be set before instantiating service';
            throw new Factory_Service_Exception($msg);
        }

        return new $class($this, $this->_clientId);
    }

    /**
     * @param string $serviceName
     * @return Service_Abstract
     */
    public function getService($serviceName)
    {
        return $this->getInstance('Service_' . $serviceName);
    }

    /**
     * @return Service_Reservation
     */
    public function getReservationService()
    {
        return $this->getService('Reservation');
    }
}