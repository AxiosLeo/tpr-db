<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-19 10:10
 */

namespace tpr\db\manager\mysql;

use tpr\db\manager\driver\Driver;

class Table extends Driver
{
    private $table_name;

    private $engine = Engine::InnoDB;

    private $auto_increment = 1;

    private $charset = Charset::Utf8;

    public function column($column_name)
    {
        $Column = new Column($this->query);
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

    public function create()
    {
        $data = [
            'table_name'     => $this->table_name,
            'engine'         => $this->engine,
            'auto_increment' => $this->auto_increment,
            'charset'        => $this->charset
        ];
        return Sql::getSql(Sql::TABLE_CREATE, $data);
    }
}