<?php
/**
 * @category tsSDK
 * @package Client
 */

namespace tsSDK;

/**
 * Abstract web service client
 */
abstract class Client_Abstract
{
    /**
     * @var string
     */
    protected $_apiKey;

    /**
     * @var string
     */
    protected $_secret;

    /**
     * @param string $apiKey
     * @param string $secret
     */
    public function __construct($apiKey, $secret)
    {
        $this->_apiKey = $apiKey;
        $this->_secret = $secret;
    }

    /**
     * Prepare arguments array for quering service
     * NB! array arguments will be imploded with "," glue
     * @param array $arguments
     * @return array
     */
    protected function _prepareArguments($arguments)
    {
        foreach ($arguments as $k => &$v) {
            if (is_array($v)) {
                $v = implode(',', $v);
            }
        }

        $arguments['apikey'] = $this->_apiKey;
        $arguments['apisig'] = Security::requestSignature($arguments, $this->_secret);
        return $arguments;
    }
}