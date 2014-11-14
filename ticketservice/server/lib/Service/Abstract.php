<?php
/**
 * @category TicketSellingService
 * @package Service
 */

namespace ts;

/**
 * Abstract service class
 */
abstract class Service_Abstract
{
    /**
     * @var Factory_Service
     */
    protected $_serviceFactory;

    /**
     * @var int
     */
    protected $_clientId;

    /**
     * Options for servers
     * @var array
     */
    protected $_serverOptions = array();

    /**
     * @param Factory_Service $serviceFactory
     * @param int $clientId 
     */
    public function __construct(Factory_Service $serviceFactory, $clientId)
    {
        $this->_serviceFactory = $serviceFactory;
        $this->_clientId = $clientId;
    }

    /**
     * @param string $serverType
     * @return array
     */
    public function getServerOptions($serverType)
    {
        if (isset($this->_serverOptions[$serverType])) {
            return $this->_serverOptions[$serverType];
        }

        return array();
    }
}