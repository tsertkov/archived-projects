<?php
/**
 * @category TicketSellingService
 * @package Bootstrap
 */

namespace ts;
use Jaer\ResourceContainer_Abstract;
use \PDO;

/**
 * @method array getConfig()
 * @method Factory_Service getServiceFactory()
 * @method Factory_Dao getDaoFactory()
 * @method Database getDb()
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
     * @return Factory_Service
     */
    public function _initServiceFactory()
    {
        return new Factory_Service($this);
    }

    /**
     * @return Factory_Dao
     */
    public function _initDaoFactory()
    {
        return new Factory_Dao($this);
    }

    /**
     * @return PDO
     */
    public function _initDb()
    {
        $config = $this->getResource('config');
        $c = $config['db'];

        $db = new PDO($config['db']['dsn']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
