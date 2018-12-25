<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-19 10:10
 */

namespace tpr\db\manager\mysql;

use tpr\db\manager\driver\Mysql;

class Table extends Mysql
{
    private $table_name;

    public function column($column_name)
    {
        $Column = new Column();
        $Column->setTableName($this->table_name)
            ->setColumnName($column_name);
        return $Column;
    }

    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
        return $this;
    }

    public function setEngine($engine)
    {
        $this->engine = $engine;
        return $this;
    }

    public function setAutoIncrement($auto_increment)
    {
        $this->auto_increment = $auto_increment;
        return $this;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    public function create($sys_table = null)
    {
        if (is_null($sys_table)) {
            $this->operation = Operation::TABLE_CREATE;

            $this->sql_data = [
                'table_name'     => $this->formatTableName($this->table_name),
                'engine'         => $this->engine,
                'auto_increment' => $this->auto_increment,
                'charset'        => $this->charset
            ];
        } else {
            $this->operation = Operation::TABLE_SYS;
            $this->sql_data  = [
                'table_name' => $this->formatTableName($this->table_name),
                'like'       => ' LIKE ' . $this->formatTableName($sys_table)
            ];
        }

        return $this;
    }

    public function delete()
    {
        $this->operation = Operation::TABLE_DELETE;
        $this->sql_data  = [
            'table_name' => $this->formatTableName($this->table_name)
        ];
        return $this;
    }

    /**
     * 同步数据表数据，仅支持同mysql实例
     * @param      $source_db
     * @param null $source_table
     *
     * @return $this
     * @throws \ErrorException
     * @throws \tpr\db\exception\BindParamException
     * @throws \tpr\db\exception\Exception
     * @throws \tpr\db\exception\PDOException
     */
    public function sysData($source_db, $source_table = null)
    {
        $curr_db = $this->dbName();
        $this->dbName($source_db);
        $sql = $this->query->table($source_db . '.' . $source_table)->fetchSql()->select();
        $this->dbName($curr_db);
        $this->operation = Operation::TABLE_SYS_DATA;
        $this->sql_data  = [
            'table_name' => $this->formatTableName($this->table_name),
            'sql'        => $sql
        ];
        return $this;
    }
}