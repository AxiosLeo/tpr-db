<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 16:38
 */

namespace tpr\db\manager\mysql;

class Database
{
    const CREATE_DATABASE = 'db.create';

    private $db_name;

    private $charset;

    private $collate;

    public function __construct($quer)
    {
        $this->query = $query;
    }

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

    public function create()
    {
        $data = [
            'name'    => $this->db_name,
            'charset' => $this->charset,
            'collate' => $this->collate
        ];
        $sql  = Sql::getSql(self::CREATE_DATABASE, $data);
    }
}