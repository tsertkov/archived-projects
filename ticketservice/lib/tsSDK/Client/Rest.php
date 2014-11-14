<?php
/**
 * @category tsSDK
 * @package Client
 */

namespace tsSDK;

/**
 * Rest client
 */
class Client_Rest extends Client_Abstract
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    protected $_endpoint;

    /**
     * @var resource
     */
    protected $_curl;

    /**
     * @var array
     */
    protected $_curlOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_USERAGENT => 'TicketServiceClient PHP-SDK',
    );

    /**
     * @param string $endpoint
     * @param string $apiKey
     * @param string $secret
     */
    public function __construct($endpoint, $apiKey, $secret)
    {
        $this->_endpoint = $endpoint;
        parent::__construct($apiKey, $secret);
    }

    /**
     * @param array $options
     */
    public function addCurlOptions(array $options)
    {
        $this->_curlOptions = $options + $this->_curlOptions;
    }

    /**
     * @param string $httpMethod
     * @param array $arguments
     * @return resource curl resource
     * @throws Client_Exception
     */
    protected function _getCurl($httpMethod, array $arguments)
    {
        if (null === $this->_curl) {
            $this->_curl = curl_init();
            curl_setopt_array($this->_curl, $this->_curlOptions);
        }

        switch ($httpMethod) {
            case self::METHOD_GET:
                $url = $this->_endpoint . '?' . http_build_query($arguments);
                curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
                break;

            case self::METHOD_POST:
                $url = $this->_endpoint;
                curl_setopt($this->_curl, CURLOPT_POST, true);
                curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $arguments);
                break;

            default:
                $msg = sprintf('Unknown http request method specified: <%s>', $httpMethod);
                throw new Client_Exception($msg);
        }

        curl_setopt($this->_curl, CURLOPT_URL, $url);
        return $this->_curl;
    }

    /**
     * Execute REST request
     * @param string $httpMethod
     * @param string $method
     * @param array $arguments
     * @return string
     * @throws Client_Exception
     */
    public function request($httpMethod, $method, array $arguments)
    {
        $arguments['method'] = $method;
        $arguments = $this->_prepareArguments($arguments);
        $curl = $this->_getCurl($httpMethod, $arguments);
        $response = curl_exec($curl);

        if (false === $response) {
            throw new Client_Exception('Could not request REST server. Curl complains: '
                . curl_error($curl));
        }

        if (200 != curl_getinfo($this->_curl, CURLINFO_HTTP_CODE)) {
            $this->_processResponseError($response);
        }

        return $response;
    }

    /**
     * Process error response
     * @param string $response
     * @throws Server_Exception
     * @throws Service_Exception
     */
    protected function _processResponseError($response)
    {
        if (false === ($pos = strpos($response, "\n"))) {
            throw new Server_Exception($response);
        }

        $exception = substr($response, 0, $pos);
        $message = substr($response, $pos + 1);

        if ($exception == 'ts\\Service_Exception') {
            throw new Service_Exception($exception . ': ' . $message);
        }

        throw new Server_Exception($exception . ': ' . $message);
    }

    /**
     * Execute GET request
     * @param string $method
     * @param array $arguments
     * @return string
     */
    public function get($method, array $arguments = array())
    {
        return $this->request(self::METHOD_GET, $method, $arguments);
    }

    /**
     * Execute POST request
     * @param string $method
     * @param array $arguments
     * @return string
     */
    public function post($method, array $arguments = array())
    {
        return $this->request(self::METHOD_POST, $method, $arguments);
    }
}