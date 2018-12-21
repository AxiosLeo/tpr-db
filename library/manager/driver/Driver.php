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

    public function __construct()
    {
        if (is_null(self::$options)) {
            self::$options = ArrayTool::instance([]);
        }
        $this->query = self::$queryInstance;
    }

    public function setQuery($query)
    {
        $this->query         = $query;
        self::$queryInstance = $query;
        $this->setOption(self::$queryInstance->getConfig());
        return $this;
    }

    public function getQuery()
    {
        return self::$queryInstance;
    }

    public function setOption($key, $value = null)
    {
        self::$options->set($key, $value);
    }

    public function getOptions($key = null, $default = null)
    {
        return self::$options->get($key, $default);
    }
}