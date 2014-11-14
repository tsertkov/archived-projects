<?php
/**
 * @category Jaer
 * @package ResourceContainer
 */

namespace Jaer;

/**
 * Abstract dependency injection container
 * @method Application_Abstract getApplication()
 */
abstract class ResourceContainer_Abstract
{
    /**
     * Array of initialized resources
     * @var array
     */
    protected $_resources = array();

    /**
     * @param Application_Abstract $application
     */
    public function __construct(Application_Abstract $application)
    {
        $this->setResource('Application', $application);
    }

    /**
     * Set resource
     * @param string $resourceName
     * @param mixed $resource
     */
    public function setResource($resourceName, $resource)
    {
        $this->_resources[$resourceName] = $resource;
    }

    /**
     * Get resource by name
     * @param string $resourceName
     * @return mixed
     * @throws ResourceContainer_Exception
     */
    public function getResource($resourceName)
    {
        if (isset($this->_resources[$resourceName])) {
            return $this->_resources[$resourceName];
        }

        $method = '_init' . $resourceName;
        if (!method_exists($this, $method)) {
            $msg = sprintf('Could not find initialization method for resource <%s>', $resourceName);
            throw new ResourceContainer_Exception($msg);
        }

        $resource = $this->$method();
        $this->_resources[$resourceName] = $resource;
        return $resource;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ResourceContainer_Exception
     */
    public function __call($name, $arguments)
    {
        if (strncmp('get', $name, 3)) {
            $msg = sprintf('Method <%s::%s()> does not exists', get_class($this), $name);
            throw new ResourceContainer_Exception($msg);
        }

        $resourceName = substr($name, 3);
        return $this->getResource($resourceName);
    }
}