<?php
/**
 * @category Jaer
 * @package Application
 */

namespace Jaer;

/**
 * Abstract application with resources lazy loading capabilities
 */
abstract class Application_Abstract
{
    /**
     * Path to application folder
     * @var string
     */
    protected $_appPath;

    /**
     * Current environment
     * @var string
     */
    protected $_environment;

    /**
     * @var ResourceContainer_Abstract
     */
    protected $_resourceContainer;

    /**
     * @param string $appPath
     * @param string $environment
     */
    public function __construct($appPath, $environment)
    {
        $this->_appPath = $appPath;
        $this->_environment = $environment;
    }

    /**
     * Returns application resource container
     * @return ResourceContainer_Abstract
     */
    public function getResourceContainer()
    {
        if (null === $this->_resourceContainer) {
            $this->_resourceContainer = $this->_initResourceContainer();
        }

        return $this->_resourceContainer;
    }

    /**
     * Default resouce container initialization
     * @return ResourceContainer_Abstract
     */
    protected function _initResourceContainer()
    {
        $class = get_class($this);
        if (false !== $pos = strrpos($class, '\\')) {
            $class = substr($class, 0, $pos + 1) . 'ResourceContainer';
        } else {
            $class = 'ResourceContainer';
        }

        return new $class($this);
    }

    /**
     * Returns current environment name
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Returns full path to application folder
     * @return string
     */
    public function getAppPath()
    {
        return $this->_appPath;
    }
}