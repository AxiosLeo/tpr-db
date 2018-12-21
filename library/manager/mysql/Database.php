<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 16:38
 */

namespace tpr\db\manager\mysql;

use tpr\db\manager\driver\Mysql;

class Database extends Mysql
{
    public function setCharSet($charset = null)
    {
        $this->charset = $charset;
        return $this;
    }

    public function setCollate($collate)
    {
        $this->collate = $collate;
        return $this;
    }

    public function table($table_name)
    {
        $Table = new Table();
        $Table->setTableName($table_name);
        $Table->dbName($this->dbName());
        return $Table;
    }

    public function getTableList()
    {
        $sql    = Sql::getSql(Operation::TABLE_SHOW, [
            "name" => $this->dbName()
        ]);
        $tables = $this->query->query($sql);
        $list   = [];
        foreach ($tables as $t) {
            foreach ($t as $k => $v) {
                array_push($list, $v);
            }
        }
        return $list;
    }

    public function create()
    {
        $this->sql_data  = [
            'name'    => $this->dbName(),
            'charset' => $this->charset,
            'collate' => $this->charset . $this->collate
        ];
        $this->operation = Operation::DB_CREATE;
        return $this;
    }

    public function delete()
    {
        $this->sql_data  = [
            'name' => $this->formatDbName($this->dbName())
        ];
        $this->operation = Operation::DB_DELETE;
        return $this;
    }
}