<?php
/**
 * @category TicketSellingService
 * @package Dao
 */

namespace ts;
use \PDO;
use \PDOException;

/**
 * Reservation DAO class
 */
class Dao_Reservation extends Dao_Abstract
{
    /**
     * Total number of seats available
     */
    const SEATS_NUMBER = 60;

    /**
     * @return int
     */
    public function getFreeSeatsNum()
    {
        return self::SEATS_NUMBER - $this->getReservedSeatsNum();
    }

    /**
     * @return int
     */
    public function getReservedSeatsNum()
    {
        $sth = $this->_db->prepare('SELECT count(*) FROM "reservation"');
        $sth->execute();
        return $sth->fetchColumn();
    }

    /**
     * @return array
     */
    public function getFreeSeats()
    {
        $seats = array_keys(array_fill(1, 60, null));
        return array_diff($seats, $this->getReservedSeats());
    }

    /**
     * @return array
     */
    public function getReservedSeats()
    {
        $sth = $this->_db->prepare('SELECT "seat" FROM "reservation"');
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param int $clientId
     * @param string $name
     * @param array $seats
     * @return bool
     */
    public function reserveSeats($clientId, $name, array $seats)
    {
        $seatsNum = count($seats);
        if (!$seatsNum) {
            return false;
        }

        $sql = 'INSERT INTO "reservation" ("clientId", "name", "seat") VALUES (?, ?, ?)';

        try {
            $this->_db->beginTransaction();
            foreach ($seats as $seat) {
                $bind = array($clientId, $name, $seat);
                $sth = $this->_db->prepare($sql);
                $sth->execute($bind);
            }
            $this->_db->commit();

        } catch (PDOException $e) {
            $this->_db->rollBack();

            // seat already taken (unique violation)
            if (in_array($e->getCode(), array(23505, 23000))) {
                return false;
            }

            throw $e;
        }

        return true;
    }
}