<?php
/**
 * @category tsSDK
 * @package Service
 */

namespace tsSDK;

/**
 * Abstract service
 */
abstract class Service_Rest_Abstract extends Service_Abstract
{
    /**
     * @param string $httpMethod
     * @param string $method
     * @param array $arguments
     * @return string
     */
    protected function _request($httpMethod, $method, $arguments)
    {
        $arguments = $this->_mapArguments($method, $arguments);
        $method = $this->_serviceName() . '.' . $method;
        return $this->_client->request($httpMethod, $method, $arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return string
     */
    protected function _get($method, array $arguments = array())
    {
        return $this->_request(Client_Rest::METHOD_GET, $method, $arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return string
     */
    protected function _post($method, array $arguments = array())
    {
        return $this->_request(Client_Rest::METHOD_POST, $method, $arguments);
    }
}