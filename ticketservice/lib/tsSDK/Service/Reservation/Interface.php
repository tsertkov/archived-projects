<?php
/**
 * @category tsSDK
 * @package Service
 */

namespace tsSDK;

/**
 * Reservation service interface
 */
interface Service_Reservation_Interface
{
    /**
     * Returns total number of free seats available
     * @return int
     */
    public function getFreeSeatsNum();

    /**
     * Returns list of requested number of free seats
     * @param int $number number of free seats to return
     * @return array array of available free seats numbers
     * @throws Service_Exception
     */
    public function getFreeSeatsList($number);

    /**
     * Reserve seats for user
     * @param string $name user name
     * @param array $seats array of seats numbers to reserve
     * @throws Service_Exception
     */
    public function reserveSeats($name, array $seats);
}