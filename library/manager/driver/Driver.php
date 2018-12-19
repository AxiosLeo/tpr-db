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
     * @var array
     */
    protected static $sql = [];

    /**
     * @var Connection
     */
    protected static $query;

    /**
     * @var array
     */
    protected static $result = [];

    public function __construct()
    {
        if (is_null(self::$options)) {
            self::$options = ArrayTool::instance([]);
        }
    }

    public function setQuery($query)
    {
        self::$query = $query;
        $this->setOption(self::$query->getConfig());
        return $this;
    }

    public function setOption($key, $value = null)
    {
        self::$options->set($key, $value);
    }

    public function getOptions($key = null, $default = null)
    {
        return self::$options->get($key, $default);
    }

    protected function pushSql($sql)
    {
        // 判断是否已存在相同sql,避免重复操作
        if (!in_array($sql, self::$sql)) {
            array_push(self::$sql, $sql);
        }
        return $this;
    }

    public function execSql()
    {
        foreach (self::$sql as $sql) {
            self::$result[$sql] = self::$query->query($sql);
        }
        return self::$result;
    }

    public function viewSql()
    {
        return self::$sql;
    }
}