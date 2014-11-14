<?php
/**
 * @category TicketSellingClient
 * @package Service
 */

namespace tsSDK;

/**
 * Reservation service client
 */
class Service_Rest_Reservation extends Service_Rest_Abstract implements Service_Reservation_Interface
{
    public function getFreeSeatsNum()
    {
        return $this->_get(__FUNCTION__);
    }

    public function getFreeSeatsList($number)
    {
        return explode(',', $this->_get(__FUNCTION__, func_get_args()));
    }

    public function reserveSeats($name, array $seats)
    {
        $this->_post(__FUNCTION__, func_get_args());
    }
}