<?php
/**
 * @category TicketSellingService
 * @package Dao
 */

namespace ts;
use \PDO;

/**
 * Abstract DAO class
 */
abstract class Dao_Abstract
{
    /**
     * @var PDO
     */
    protected $_db;

    /**
     * @var Factory_Dao
     */
    protected $_daoFactory;

    /**
     * @param PDO $db
     */
    public function __construct(Factory_Dao $daoFactory, PDO $db)
    {
        $this->_daoFactory = $daoFactory;
        $this->_db = $db;
    }
}