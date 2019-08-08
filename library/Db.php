<?php

namespace tpr\db;

use think\DbManager;

class Db
{
    /**
     * @var int 查询次数
     */
    public static $queryTimes = 0;

    /**
     * @var int 执行次数
     */
    public static $executeTimes = 0;
    /**
     * @var array 数据库连接实例
     */
    private static $instance = [];

    /**
     * @param array  $config
     * @param string $name
     *
     * @return DbManager
     */
    public static function connect($config = [], $name = 'default')
    {
        $db = new DbManager();
        if (!isset($config['type']) || empty($config['type'])) {
            $type = 'mysql';
        } else {
            $type = $config['type'];
        }
        $connections = [
            $type => $config,
        ];
        $db->setConfig([
            'default'     => $type,
            'connections' => $connections,
        ]);
        self::$instance[$name] = $db;

        return self::$instance[$name];
    }

    /**
     * @param string $name
     *
     * @return DbManager
     */
    public static function db($name = 'default')
    {
        if (!isset(self::$instance[$name])) {
            return null;
        }

        return self::$instance[$name];
    }

    /**
     * @param null $name
     */
    public static function clear($name = null): void
    {
        if (null === $name) {
            self::$instance = null;
        } else {
            unset(self::$instance[$name]);
        }
    }
}
