<?php
/**
 * @category TicketSellingService
 * @package Server
 */

namespace ts;
use tsSDK\Security;

/**
 * Abstract web services server
 */
abstract class Server_Abstract
{
    /**
     * @var ResourceContainer
     */
    protected $_resourceContainer;

    /**
     * Default service name
     * @var string
     */
    protected $_defServiceName;

    /**
     * @param ResourceContainer $resourceContainer
     */
    public function __construct(ResourceContainer $resourceContainer)
    {
        $this->_resourceContainer = $resourceContainer;
    }

    /**
     * Set default service name
     * @param string $serviceName
     */
    public function setDefServiceName($serviceName)
    {
        $this->_defServiceName = $serviceName;
    }

    /**
     * Dispath web service request
     * @param Request $request
     * @return array
     */
    abstract protected function _getRequestArguments(Request $request);

    /**
     * @param mixed $response
     */
    abstract protected function _handleResponse($response);

    /**
     * @param Exception $exception
     */
    abstract protected function _handleException(\Exception $exception);

    /**
     * Returns an array of arguments suitable to be called via call_user_func_array
     * @param Service_Abstract $service
     * @param string $method
     * @param array $arguments
     * @return array
     * @throws Server_Exception
     */
    protected function _mapArguments(Service_Abstract $service, $method, array $arguments)
    {
        $rMethod = new \ReflectionMethod(get_class($service), $method);

        $methodArgs = array();
        $missingArgs = array();
        foreach ($rMethod->getParameters() as $rParameter) {
            if (isset($arguments[$rParameter->getName()])) {
                $parameter = $arguments[$rParameter->getName()];

                if ($rParameter->isArray()) {
                    $parameter = explode(',', $parameter);
                }

                $methodArgs[] = $parameter;
                continue;
            }

            if ($rParameter->isOptional()) {
                $methodArgs[] = $rParameter->getDefaultValue();
            } else {
                $missingArgs[] = $rParameter->getName();
            }
        }

        if ($missingArgs) {
            $msg = sprintf('Required parameters not set: <%s>', array_implode(', ', $missingArgs));
            throw new Server_Exception($msg);
        }

        return $methodArgs;
    }

    /**
     * Check request signature
     * @param array $arguments
     * @param string $apiKey
     * @param string $apiSig
     * @throws Server_Exception
     */
    protected function _checkRequestSignature(array $arguments, $apiKey, $apiSig)
    {
        $secret = $this->_resourceContainer->getDaoFactory()->getClientDao()->getSecretByApiKey($apiKey);

        if (!$secret) {
            throw new Server_Exception('Unknown apikey');
        }

        if ($apiSig != Security::requestSignature($arguments, $secret)) {
            $msg = 'Invalid request signature';
            throw new Server_Exception($msg);
        }
    }

    /**
     * Checks if all required request parameters set
     * @param array $arguments
     * @throws Server_Signature
     */
    protected function _checkRequiredArguments(array $arguments)
    {
        foreach (array('apikey', 'method', 'apisig') as $v) {
            if (!isset($arguments[$v])) {
                $msg = "Required request parameter <$v> was not set";
                throw new Server_Exception($msg);
            }
        }
    }

    /**
     * Handle web service request
     * @param Request $request
     * @throws Server_Exception
     */
    protected function _handleRequest(Request $request)
    {
        //
        // get request arguments and validate request
        //

        $arguments = $this->_getRequestArguments($request);
        $this->_checkRequiredArguments($arguments);

        $apiKey = $arguments['apikey'];
        $method = $arguments['method'];
        $apiSig = $arguments['apisig'];
        unset($arguments['apisig']);

        $this->_checkRequestSignature($arguments, $apiKey, $apiSig);

        unset($arguments['apikey']);
        unset($arguments['method']);

        // validate request method
        if (false === ($pos = strpos($method, '.'))) {
            if (!$this->_defServiceName) {
                $msg = 'Invalid method name <%s>. No service specified and no default service set.';
                throw new Server_Exception($msg);
            }

            $service = $this->_defServiceName;

        } else {
            $service = substr($method, 0, $pos);
            $method = substr($method, $pos + 1);
        }

        $clientId = $this->_resourceContainer->getDaoFactory()->getClientDao()->getClientByApiKey($apiKey);

        if (null == $clientId) {
            throw new Server_Exception('Unknown apikey');
        }

        $this->_resourceContainer->getServiceFactory()->setClientId($clientId);

        $service = $this->_resourceContainer->getServiceFactory()->getService($service);
        $arguments = $this->_mapArguments($service, $method, $arguments);

        // last check before executing service method
        $this->_checkRequest($service, $method, $request);

        $response = call_user_func_array(array($service, $method), $arguments);
        if (is_array($response)) {
            $response = implode(',', $response);
        }

        $this->_handleResponse($response);
    }

    /**
     * Check if requested method mey be called
     * @param Service_Abstract $service
     * @param string $method
     * @param Request $request
     * @throws Server_Exception
     */
    protected function _checkRequest(Service_Abstract $service, $method, Request $request)
    {
        if (!method_exists($service, $method)) {
            $msg = sprintf('Unknown method <%s> requested', $method);
            throw new Server_Exception($msg);
        }
    }

    /**
     * Handle web service request
     * @param Request $request
     */
    public function handle(Request $request = null)
    {
        try {
            if (!$request) {
                $request = new Request();
            }

            $this->_handleRequest($request);

        } catch (\Exception $e) {
            $this->_handleException($e);
        }
    }
}