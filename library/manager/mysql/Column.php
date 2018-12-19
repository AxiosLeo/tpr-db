<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-19 14:12
 */

namespace tpr\db\manager\mysql;

use tpr\db\manager\driver\Driver;

class Column extends Driver
{
    private $table_name;

    private $column_name;

    private $datatype = "";

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

    public function setDataType($charset = '', $collate = '')
    {
        $data = [
            'charset' => $charset,
            'collate' => $collate
        ];
        Sql::getSql(Sql::DATATYPE, $data);
    }

    public function add()
    {
        $data = [
            'table_name'  => $this->table_name,
            'column_name' => '`' . $this->column_name . '`',
            'datatype'    => $this->datatype
        ];
        $sql  = Sql::getSql(Sql::COLUMN_ADD, $data);
        return $sql;
    }
}