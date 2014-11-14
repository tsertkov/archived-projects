<?php
/**
 * @category Jaer
 * @package View
 */

namespace Jaer;

class View
{
    /**
     * @var array
     */
    protected $_vars = array();

    /**
     * @var string
     */
    protected $_templatesDir;

    /**
     * @param string $templatesDir
     */
    public function __construct($templatesDir)
    {
        $this->_templatesDir = $templatesDir;
    }

    /**
     * Render template
     * @param string $template
     */
    public function render($template)
    {
        extract($this->_vars);
        $path = $this->_templatesDir . '/' . $template . '.phtml';

        $er = error_reporting(E_ALL & ~ E_NOTICE);
        include_once $path;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_vars[$name] = $value;
    }
}