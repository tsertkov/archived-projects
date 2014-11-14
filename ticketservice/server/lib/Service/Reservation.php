<?php
/**
 * @category TicketSellingService
 * @package Service
 */

namespace ts;
use tsSDK\Service_Reservation_Interface;

/**
 * Reservation service
 */
class Service_Reservation extends Service_Abstract implements Service_Reservation_Interface
{
    protected $_serverOptions = array(
        'Rest' => array(
            // allow only POST requests to reserveSeats method
            'reserveSeats' => Request::METHOD_POST,
        ),
    );

    /**
     * @return Dao_Reservation
     */
    protected function _reservationDao()
    {
        return $this->_serviceFactory->getResourceContainer()->getDaoFactory()->getReservationDao();
    }

    public function getFreeSeatsNum()
    {
        return $this->_reservationDao()->getFreeSeatsNum();
    }

    public function getFreeSeatsList($number)
    {
        if (!is_numeric($number)) {
            throw new Service_Exception('Number of seats should be numeric value!');
        }

        if ($number <= 0) {
            throw new Service_Exception('Number of seats should be greater than zero!');
        }

        $freeSeats = $this->_reservationDao()->getFreeSeats();
        
        if (count($freeSeats) < $number) {
            $msg = sprintf('Not enough free seats available <%s>, requested <%s>', count($freeSeats)
                , $number);
            throw new Service_Exception($msg);
        }

        return array_rand(array_flip($freeSeats), $number);
    }

    public function reserveSeats($name, array $seats)
    {
        $name = trim($name);
        if (strlen($name) < 2) {
            $msg = 'Name should be at lest two characters long';
            throw new Service_Exception($msg);
        }

        $r = $this->_reservationDao()->reserveSeats($this->_clientId, $name, $seats);
        if (!$r) {
            $msg = 'Could not reserve seats because some of them are already reserved';
            throw new Service_Exception($msg);
        }
    }
}