<?php
/**
 * @category TicketSellingService
 * @package Factory
 */

namespace ts;
use \Jaer\Factory_Abstract;

/**
 * Dao factory
 */
class Factory_Dao extends Factory_Abstract
{
    protected function _createInstance($class)
    {
        return new $class($this, $this->_resourceContainer->getDb());
    }

    /**
     * @return Dao_Reservation
     */
    public function getReservationDao()
    {
        return $this->getInstance('Dao_Reservation');
    }

    /**
     * @return Dao_Client
     */
    public function getClientDao()
    {
        return $this->getInstance('Dao_Client');
    }
}