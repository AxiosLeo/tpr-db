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

    protected $sql = [];

    /**
     * @var Connection
     */
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
        if (is_null($this->options)) {
            $this->options = ArrayTool::instance($this->query->getConfig());
        }
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
        array_push($this->sql, $sql);
    }

//    abstract public function createDatabase($name);
//
//    abstract public function createTable();
//
//    abstract public function createField();
}