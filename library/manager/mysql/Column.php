<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-19 14:12
 */

namespace tpr\db\manager\mysql;

use tpr\db\manager\driver\Mysql;

class Column extends Mysql
{
    private $table_name;

    private $column_name;

    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
        return $this;
    }

    public function setColumnName($column_name)
    {
        $this->column_name = $column_name;
        return $this;
    }

    public function add()
    {
        $data = [
            'table_name'  => $this->formatTableName($this->table_name),
            'column_name' => $this->formatTableName($this->column_name),
            'datatype'    => $this->getDataType()
        ];
        $sql  = Sql::getSql(Sql::COLUMN_ADD, $data);
        $this->pushSql($sql);
        return $sql;
    }
}