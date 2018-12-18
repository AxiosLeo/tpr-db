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

    private function getSql()
    {
        $db_name = $this->options->get('db.db_name', '');

        if ($this->options->get('db.force')) {
            $sql = "DROP TABLE IF EXISTS `" . $db_name . "`;";
            $this->pushSql($sql);
        }

        $charset = $this->options->get('db.charset', 'utf8');
        $collate = $this->options->get('db.collate', 'utf8_general_ci');
        $sql     = "create database if not exists `$db_name` default character set $charset collate $collate;";
        $this->pushSql($sql);

        $sql = "CREATE TABLE `api_admin` () ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;";
        $this->pushSql($sql);
    }

    private function pushSql($sql)
    {
        array_push($this->sql, $sql);
    }

    abstract public function createDatabase();

    abstract public function createTable();

    abstract public function createField();
}