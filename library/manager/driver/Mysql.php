<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:55
 */

namespace tpr\db\manager\driver;

use tpr\db\manager\mysql\Database;

class Mysql extends Driver
{
    public function database($name, $charset = null, $collate = null)
    {
        $Database = new Database($this->query, $this->options);
        $Database->setDatabaseName($name);
        $charset = is_null($charset) ? $this->options->get('charset', 'utf8') : $charset;
        $Database->setCharSet($charset);
        $collate = is_null($collate) ? $charset . '_general_ci' : $collate;
        $Database->setCollate($collate);
        return $Database;
    }

    public function createTable()
    {
        // TODO: Implement createTable() method.
    }

    public function createField()
    {
        // TODO: Implement createField() method.
    }

    public function query($sql){
        $this->query->query($sql);
    }

    public function create(){

    }
}