<?php

namespace tpr\db;

use tpr\db\exception\DatabaseTypeErrorException;
use tpr\db\manager\driver\Mysql;

/**
 * Class DbManager.
 *
 * @method Mysql mysql($con_name = "", $config = [])
 */
class DbManager
{
    protected $support = [
        'mysql',
    ];

    /**
     * @var Mysql[]
     */
    private $driver = [];

    public function __call($name, $arguments)
    {
        array_unshift($arguments, $name);

        return \call_user_func_array([$this, 'initDriver'], $arguments);
    }

    /**
     * @return DbManager
     */
    public static function instance()
    {
        return new self();
    }

    protected function initDriver($db_type = '', $con_name = '', $config = [])
    {
        if (!isset($this->driver[$con_name])) {
            $config['type'] = $db_type;
            $this->checkType($db_type);
            $class = 'tpr\\db\\manager\\driver\\' . ucfirst(strtolower($db_type));

            $this->driver[$con_name] = new $class();
            $this->driver[$con_name]->setOption($config);
        } elseif (!empty($config)) {
            $this->driver[$con_name]->setOption($config);
        }

        return $this->driver[$con_name];
    }

    private function checkType($db_type)
    {
        if (!\in_array($db_type, $this->support)) {
            $support = implode('|', $this->support);

            throw new DatabaseTypeErrorException('Database Type are not supported!  Only support ' . $support);
        }
    }
}
