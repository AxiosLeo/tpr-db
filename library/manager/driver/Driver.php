<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:57
 */

namespace tpr\db\manager\driver;

use tpr\db\core\ArrayTool;
use tpr\db\core\Connection;
use tpr\db\DbClient;

abstract class Driver
{
    /**
     * @var ArrayTool
     */
    protected static $options;

    /**
     * @var Connection
     */
    protected static $queryInstance;

    protected $query;

    protected static $con_name_static;

    protected $con_name;

    public function __construct()
    {
        if (is_null(self::$options)) {
            self::$options = ArrayTool::instance([]);
        }
        $this->query    = $this->getQuery();
        $this->con_name = $this->getConName();
    }

    public function setConName($con_name)
    {
        self::$con_name_static = $con_name;
        return $this;
    }

    public function getConName()
    {
        return self::$con_name_static;
    }

    public function setQuery($query)
    {
        $this->query         = $query;
        self::$queryInstance = $query;
        return $this;
    }

    public function getQuery()
    {
        if (is_null(self::$queryInstance)) {
            $this->setQuery(DbClient::newCon($this->getConName(), $this->getOptions()));
        }
        return self::$queryInstance;
    }

    public function setOption($key, $value = null)
    {
        self::$options->set($key, $value);

        DbClient::closeCon($this->getConName());

        $query = DbClient::newCon($this->getConName(), $this->getOptions());

        $this->setQuery($query);

        return $this;
    }

    public function getOptions($key = null, $default = null)
    {
        return self::$options->get($key, $default);
    }
}