<?php

namespace tpr\db\manager\driver;

use think\DbManager;
use tpr\db\core\ArrayTool;
use tpr\db\DbClient;

abstract class Driver
{
    /**
     * @var ArrayTool
     */
    protected static $options;

    /**
     * @var DbManager
     */
    protected static $queryInstance;

    /**
     * @var DbManager
     */
    protected $query;

    protected static $con_name_static;

    protected $con_name;

    public function __construct()
    {
        if (null === self::$options) {
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
        if (null === self::$queryInstance) {
            $this->setQuery(DbClient::newCon($this->getConName(), $this->getOptions()));
        }

        return self::$queryInstance;
    }

    public function setOption($key, $value = null)
    {
        self::$options->set($key, $value);
        self::$options->delete('dsn');

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
