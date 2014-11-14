<?php
/**
 * @category Jaer
 * @package Factory
 */

namespace Jaer;

/**
 * Abstract factory
 */
abstract class Factory_Abstract
{
    /**
     * @var ResourceContainer_Abstract
     */
    protected $_resourceContainer;

    /**
     * Instances cache
     * @var array
     */
    protected $_instances = array();

    /**
     * Namespace to search classes in
     * @var string
     */
    protected $_namespace;

    /**
     * @param ResourceContainer_Abstract $resourceContainer
     */
    public function __construct(ResourceContainer_Abstract $resourceContainer)
    {
        $this->_resourceContainer = $resourceContainer;
        $this->_namespace = $this->_getNamespace();
    }

    /**
     * Get current factory instance namespace
     * @return string
     */
    protected function _getNamespace()
    {
        $class = get_class($this);
        if (false !== ($pos = strrpos($class, '\\'))) {
            return substr($class, 0, $pos);
        }

        return '\\';
    }

    /**
     * Get name of class to instantiate by factory
     * @param string $instanceName
     * @return string
     */
    protected function _getClass($instanceName)
    {
        if (false === strpos($instanceName, '\\')) {
            $class = ('\\' == $this->_namespace ? '' : $this->_namespace) . '\\' . $instanceName;
        } else {
            $class = $instanceName;
        }

        return $class;
    }

    /**
     * Create requested class instance
     * @param string $class
     * @return mixed class instance
     */
    abstract protected function _createInstance($class);

    /**
     * Get class instance by requested name
     * @param string $instanceName
     * @return mixed class instance
     * @throws Factory_Exception
     */
    public function getInstance($instanceName)
    {
        if (isset($this->_instances[$instanceName])) {
            return $this->_instances[$instanceName];
        }

        $class = $this->_getClass($instanceName);
        if (!class_exists($class)) {
            $msg = sprintf('Class <%s> not found', $class);
            throw new Factory_Exception($msg);
        }

        $o = $this->_createInstance($class);
        $this->_instances[$instanceName] = $o;

        return $o;
    }

    /**
     * @return ResourceContainer_Abstract
     */
    public function getResourceContainer()
    {
        return $this->_resourceContainer;
    }
}