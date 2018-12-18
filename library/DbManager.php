<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:52
 */

namespace tpr\db;

use tpr\db\exception\DatabaseTypeErrorException;
use tpr\db\manager\driver\Driver;

class DbManager
{
    /**
     * @param string $con_name
     * @param array  $config
     *
     * @return DbManager
     * @throws DatabaseTypeErrorException
     */
    public static function instance($con_name, $config = [])
    {
        return new self($con_name, $config);
    }

    protected $support = [
        'mysql'
    ];

    /**
     * @var core\Connection
     */
    private $query;

    /**
     * @var Driver
     */
    private $driver;

    public function __construct($con_name, $config = [])
    {
        $this->query = DbClient::newCon($con_name, $config);
        $db_type     = $this->query->getConfig('type');
        if (!in_array($db_type, $this->support)) {
            $support = implode('|', $this->support);
            throw new DatabaseTypeErrorException("Database Type are not supported!  Only support " . $support);
        }
        $class        = "tpr\\db\\manager\\driver\\" . ucfirst(strtolower($db_type));
        $this->driver = new $class($this->query);
    }

    /**
     * @return Driver
     */
    public function driver()
    {
        return $this->driver;
    }

    public function setOptions($key, $value = null)
    {
        $this->driver->setOption($key, $value);
        return $this;
    }

    public function getOptions($key = null, $default = null)
    {
        return $this->driver->getOptions($key, $default);
    }
}