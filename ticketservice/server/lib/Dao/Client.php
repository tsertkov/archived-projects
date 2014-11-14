<?php
/**
 * @category TicketSellingService
 * @package Dao
 */

namespace ts;

/**
 * Client DAO class
 */
class Dao_Client extends Dao_Abstract
{
    /**
     * @param string $apiKey
     * @return string|FALSE
     */
    public function getClientByApiKey($apiKey)
    {
        $sth = $this->_db->prepare('SELECT "clientId" FROM "apiKey" WHERE "key"=?');
        $sth->execute(array($apiKey));
        return $sth->fetchColumn();
    }

    /**
     * @param string $apiKey
     * @return string|FALSE
     */
    public function getSecretByApiKey($apiKey)
    {
        $sth = $this->_db->prepare('SELECT "secret" FROM "apiKey" WHERE "key"=?');
        $sth->execute(array($apiKey));
        return $sth->fetchColumn();
    }
}