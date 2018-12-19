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
    private $db_name;

    /**
     * @var Database
     */
    private static $DatabaseInstance;

    public function __construct($query)
    {
        parent::__construct($query);
        $this->db_name = $this->options->get('database');
    }

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
            self::$DatabaseInstance = new Database($this->query);
            self::$DatabaseInstance->setDatabaseName($db_name);
        } elseif (!is_null($db_name) && $db_name != $this->db_name) {
            $this->db_name = $db_name;
            self::$DatabaseInstance->setDatabaseName($this->db_name);
        }
        return self::$DatabaseInstance;
    }
}