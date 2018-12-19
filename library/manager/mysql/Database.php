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

    private $db_name;

    private $charset = Charset::Utf8;

    private $collate = Charset::Utf8 . '_general_ci';

    public function setDatabaseName($name)
    {
        $this->db_name = $name;
        return $this;
    }

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
        $Table      = new Table();
        $Table->setTableName($table_name);
        return $Table;
    }

    public function create()
    {
        $data = [
            'name'    => $this->db_name,
            'charset' => $this->charset,
            'collate' => $this->collate
        ];
        $sql  = Sql::getSql(Sql::DB_CREATE, $data);
        $this->pushSql($sql);
        return $this;
    }

    public function delete()
    {
        $data = [
            'name' => $this->formatDbName($this->db_name)
        ];
        $sql  = Sql::getSql(Sql::DB_DELETE, $data);
        $this->pushSql($sql);
        return $this;
    }
}