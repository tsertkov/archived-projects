<?php
/**
 * @category TicketSellingService
 * @package Request
 */

namespace ts;

/**
 * Web service request
 */
class Request
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    /**
     * Request arguments
     * @var array|NULL
     */
    protected $_arguments;

    /**
     * Request method
     * @var string|NULL
     */
    protected $_requestMethod;

    /**
     * @param string|NULL $requestMethod
     * @param string|NULL $arguments
     */
    public function __construct($requestMethod = null, $arguments = null)
    {
        $this->_requestMethod = $requestMethod;
        $this->_arguments = $arguments;
    }

    /**
     * Returns request arguments array
     * @return array
     */
    public function arguments()
    {
        if (null !== $this->_arguments) {
            return $this->_arguments;
        }

        if ($this->isPost()) {
            return $_POST;
        } else if ($this->isGet()){
            return $_GET;
        }
    }

    /**
     * Returns request method
     * @return string
     */
    public function requestMethod()
    {
        if (null !== $this->_requestMethod) {
            return $this->_requestMethod;
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return self::METHOD_POST == $this->requestMethod();
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return self::METHOD_GET == $this->requestMethod();
    }
}