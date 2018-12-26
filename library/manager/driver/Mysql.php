<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:55
 */

namespace tpr\db\manager\driver;

use tpr\db\core\ArrayTool;
use tpr\db\manager\mysql\Charset;
use tpr\db\manager\mysql\Collate;
use tpr\db\manager\mysql\Database;
use tpr\db\manager\mysql\Engine;
use tpr\db\manager\mysql\Operation;
use tpr\db\manager\mysql\Sql;

class Mysql extends Driver
{
    protected $db_name;

    protected $curr_sql = null;

    protected $operation = null;

    protected $sql_data = [];

    /**
     * @var Database
     */
    private static $DatabaseInstance;

    protected $charset = Charset::Utf8;

    protected $collate = Collate::General;

    protected $engine = Engine::InnoDB;

    protected $auto_increment = 1;

    public function dbName($db_name = null)
    {
        if (is_null($db_name)) {
            return $this->db_name;
        } else if ($db_name != $this->db_name) {
            $this->db_name = $db_name;
            $this->setOption('database', $this->db_name);
        }
        return $this->db_name;
    }

    public function dbExist($db_name)
    {
        $sql    = Sql::getSql('db.exist', [
            'name' => "'" . $db_name . "'"
        ]);
        $result = $this->query->query($sql);
        $t      = new ArrayTool($result);
        $r      = $t->get('0.exist', 0) ? true : false;
        unset($t);
        unset($sql);
        return $r;
    }

    public function tableExist($db_name, $table_name)
    {
        $sql    = Sql::getSql('table.exist', [
            'name'       => "'" . $db_name . "'",
            'table_name' => "'" . $table_name . "'",
        ]);
        $result = $this->query->query($sql);
        return empty($result) ? false : true;
    }

    /**
     * @param null $db_name
     *
     * @return Database
     */
    public function database($db_name = null)
    {
        if (is_null($db_name)) {
            $db_name = $this->dbName();
        }
        if (is_null(self::$DatabaseInstance)) {
            self::$DatabaseInstance = new Database();
            self::$DatabaseInstance->dbName($db_name);
        } elseif (!is_null($db_name) && $db_name != $this->dbName()) {
            self::$DatabaseInstance->dbName($this->dbName($db_name));
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

        return Sql::getSql(Operation::DATATYPE, $data);
    }

    protected function formatDbName($db_name)
    {
        return '`' . $db_name . '`';
    }

    protected function formatTableName($table_name)
    {
        $prefix = $this->query->getConfig('prefix', '');
        if (strpos($table_name, '.') !== false) {
            list($db_name, $table_name) = explode('.', $table_name);
        } else {
            $db_name = $this->dbName();
        }

        return $this->formatDbName($db_name) . '.`' . $prefix . $table_name . '`';
    }

    protected function formatColumn($column_name)
    {
        return '`' . $column_name . '`';
    }

    public function buildSql()
    {
        $this->curr_sql = Sql::getSql($this->operation, $this->sql_data);
        return $this->curr_sql;
    }

    public function exec()
    {
        $sql = $this->buildSql();
        return $this->query->query($sql);
    }

    public function execSql($sql)
    {
        return $this->query->query($sql);
    }

    protected function clear()
    {
        $this->curr_sql  = null;
        $this->sql_data  = [];
        $this->operation = null;
    }
}