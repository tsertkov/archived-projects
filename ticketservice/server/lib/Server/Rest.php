<?php
/**
 * @category TicketSellingService
 * @package Server
 */

namespace ts;

/**
 * Rest server
 */
class Server_Rest extends Server_Abstract
{
    protected function _checkRequest(Service_Abstract $service, $method, Request $request)
    {
        parent::_checkRequest($service, $method, $request);

        $options = $service->getServerOptions('Rest');
        if (!isset($options[$method])) {
            return;
        }

        $httpMethod = $options[$method];
        if ($request->requestMethod() != $httpMethod) {
            $msg = sprintf('Method <%s> accepts only <%s> requests', $method, $httpMethod);
            throw new Server_Exception($msg);
        }
    }

    protected function _getRequestArguments(Request $request)
    {
        return $request->arguments();
    }

    /**
     * Set output http headers
     * @param array $headers
     */
    protected function _setHeaders(array $headers = array())
    {
        header('Content-Type: text/plain');
        foreach ($headers as $header) {
            header($header, true);
        }
    }

    protected function _handleResponse($response)
    {
        $this->_setHeaders();
        echo $response;
    }

    protected function _handleException(\Exception $exception)
    {
        if ($exception instanceof Service_Exception) {
            $status = 'HTTP/1.0 500 Service Exception';

        } else if ($exception instanceof Server_Exception) {
            $status = 'HTTP/1.0 500 Invalid request';

        } else {
            $status = 'HTTP/1.0 500 Internal Server Exception';
        }

        $this->_setHeaders(array($status));
        echo get_class($exception), "\n", $exception->getMessage();
    }
}