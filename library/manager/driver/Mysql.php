<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:55
 */

namespace tpr\db\manager\driver;

use tpr\db\manager\mysql\Charset;
use tpr\db\manager\mysql\Collate;
use tpr\db\manager\mysql\Database;
use tpr\db\manager\mysql\Sql;

class Mysql extends Driver
{
    private $db_name;

    /**
     * @var Database
     */
    private static $DatabaseInstance;

    protected $charset = Charset::Utf8;

    protected $collate = Collate::General;

    /**
     * @param null $db_name
     *
     * @return Database
     */
    public function database($db_name = null)
    {
        if (is_null($db_name)) {
            $db_name = $this->db_name;
        }
        if (is_null(self::$DatabaseInstance)) {
            self::$DatabaseInstance = new Database();
            self::$DatabaseInstance->setDatabaseName($db_name);
        } elseif (!is_null($db_name) && $db_name != $this->db_name) {
            $this->db_name = $db_name;
            self::$DatabaseInstance->setDatabaseName($this->db_name);
        }
        return self::$DatabaseInstance;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function setCollate($collate)
    {
        $this->collate = $collate;
    }

    protected function getDataType($charset = '', $collate = '')
    {
        if (empty($charset) && empty($collate)) {
            return '';
        }
        $data = [
            'charset' => $charset,
            'collate' => $collate
        ];

        return Sql::getSql(Sql::DATATYPE, $data);
    }

    protected function formatDbName($db_name)
    {
        return '`' . $db_name . '`';
    }

    protected function formatTableName($table_name)
    {
        $prefix  = self::$query->getConfig('prefix', '');
        $db_name =  self::$query->getConfig('database');
        return $this->formatDbName($db_name) . '.`' . $prefix . $table_name . '`';
    }

    protected function formatColumn($column_name)
    {
        return '`' . $column_name . '`';
    }
}