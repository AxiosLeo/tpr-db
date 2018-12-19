<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:52
 */

namespace tpr\db;

use tpr\db\exception\DatabaseTypeErrorException;
use \tpr\db\manager\driver\Mysql;

/**
 * Class DbManager
 * @package tpr\db
 * @method Mysql mysql($con_name = '', $config = [])
 */
class DbManager
{
    /**
     * @return DbManager
     * @throws DatabaseTypeErrorException
     */
    public static function instance()
    {
        return new self();
    }

    protected $support = [
        'mysql'
    ];

    /**
     * @var core\Connection
     */
    private $query;

    /**
     * @var Mysql
     */
    private $driver;

    protected function initDriver($db_type = '', $con_name = '', $config = [])
    {
        if (is_null($this->driver)) {
            $config['type'] = $db_type;
            $this->query    = DbClient::newCon($con_name, $config);
            $this->checkType($db_type);
            $class        = "tpr\\db\\manager\\driver\\" . ucfirst(strtolower($db_type));
            $this->driver = new $class($this->query);
            $this->driver->database($this->query->getConfig('database'));
        } elseif (!empty($config)) {
            $config_before = $this->query->getConfig();
            $config        = array_merge($config_before, $config);
            DbClient::closeCon($con_name);
            $this->query = DbClient::newCon($con_name, $config);
            $this->driver->setQuery($this->query);
            $this->driver->database($this->query->getConfig('database'));
        }
        return $this->driver;
    }

    private function checkType($db_type)
    {
        if (!in_array($db_type, $this->support)) {
            $support = implode('|', $this->support);
            throw new DatabaseTypeErrorException("Database Type are not supported!  Only support " . $support);
        }
    }

    public function __call($name, $arguments)
    {
        array_unshift($arguments, $name);
        return call_user_func_array([$this, 'initDriver'], $arguments);
    }
}