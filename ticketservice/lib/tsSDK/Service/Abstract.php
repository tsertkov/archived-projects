<?php
/**
 * @category tsSDK
 * @package Service
 */

namespace tsSDK;

/**
 * Abstract service
 */
abstract class Service_Abstract
{
    /**
     * @var Client_Rest
     */
    protected $_client;

    /**
     * @param Client_Rest $client
     */
    public function __construct(Client_Rest $client)
    {
        $this->_client = $client;
    }

    /**
     * @return string
     */
    protected function _serviceName()
    {
        $class = get_class($this);
        $serviceName = substr($class, strrpos($class, '_') + 1);

        return $serviceName;
    }

    /**
     * Map arguments to array suitable for service call
     * @param string $method
     * @param array $arguments
     * @return array
     */
    protected function _mapArguments($method, array $arguments)
    {
        $rMethod = new \ReflectionMethod(get_class($this), $method);
        $methodArgs = array();

        foreach ($rMethod->getParameters() as $rParameter) {
            $methodArgs[$rParameter->getName()] = array_shift($arguments);
        }

        return $methodArgs;
    }
}