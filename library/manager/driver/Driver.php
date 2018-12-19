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
    protected $options;

    /**
     * @var array
     */
    protected $sql = [];

    /**
     * @var Connection
     */
    protected $query;

    /**
     * @var array
     */
    protected $result = [];

    public function __construct($query)
    {
        $this->setQuery($query);
        if (is_null($this->options)) {
            $this->options = ArrayTool::instance($this->query->getConfig());
        }
    }

    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    public function setOption($key, $value = null)
    {
        $this->options->set($key, $value);
    }

    public function getOptions($key = null, $default = null)
    {
        return $this->options->get($key, $default);
    }

    protected function pushSql($sql)
    {
        // 判断是否已存在相同sql,避免重复操作
        if (!in_array($sql, $this->sql)) {
            array_push($this->sql, $sql);
        }
        return $this;
    }

    protected function execSql()
    {
        foreach ($this->sql as $sql) {
            $this->result[$sql] = $this->query->query($sql);
        }
        return $this->result;
    }

    protected function viewSql()
    {
        return $this->sql;
    }

    protected function formatTableName($table_name)
    {
        $prefix  = $this->query->getConfig('prefix', '');
        $db_name = $this->query->getConfig('database');
        return $this->formatDbName($db_name) . '.`' . $prefix . $table_name . '`';
    }

    protected function formatDbName($db_name)
    {
        return '`' . $db_name . '`';
    }
}