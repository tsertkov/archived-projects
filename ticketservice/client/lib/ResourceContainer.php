<?php
/**
 * @category TicketSellingClient
 * @package Application
 */

namespace tc;
use \Jaer\ResourceContainer_Abstract;
use \tsSDK\Client_Rest;

/**
 * @method array getConfig()
 * @method Client_Rest getRestClient()
 * @method Factory_Service getServiceFactory()
 */
class ResourceContainer extends ResourceContainer_Abstract
{
    /**
     * @return array
     */
    public function _initConfig()
    {
        $app = $this->getApplication();
        $configPath = sprintf('%s/config/%s.php', $app->getAppPath() , $app->getEnvironment());
        return require $configPath;
    }

    /**
     * @return Client_Rest
     */
    public function _initRestClient()
    {
        $config = $this->getConfig();
        $c = $config['restClient'];
        $restClient = new Client_Rest($c['endpoint'], $c['apiKey'], $c['secret']);

        if (isset($c['curlOptions'])) {
            $restClient->addCurlOptions($c['curlOptions']);
        }

        return $restClient;
    }

    /**
     * @return Factory_Service
     */
    public function _initServiceFactory()
    {
        return new Factory_Service($this);
    }

    /**
     * @return View
     */
    public function _initView()
    {
        $config = $this->getConfig();
        return new \Jaer\View(APPLICATION_PATH . '/' . $config['templatesDir']);
    }
}