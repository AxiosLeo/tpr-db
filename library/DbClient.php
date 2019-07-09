<?php

namespace tpr\db;

class DbClient
{
    private static $instance = [];

    /**
     * 动态生成数据库连接.
     *
     * @param string $con_name
     * @param array  $config
     *
     * @return \think\DbManager
     */
    public static function newCon($con_name, $config = [])
    {
        return self::instance($con_name, $config);
    }

    /**
     * 关闭连接.
     *
     * @param $con_name
     */
    public static function closeCon($con_name)
    {
        if (isset(self::$instance[$con_name])) {
            unset(self::$instance[$con_name]);
        }
    }

    /**
     * 关闭全部连接.
     */
    public static function closeAll()
    {
        foreach (self::$instance as $instance) {
            $instance->close();
        }
    }

    /**
     * 实例化连接.
     *
     * @param       $con_name
     * @param array $config
     *
     * @return \think\DbManager
     */
    private static function instance($con_name, $config = [])
    {
        if (!isset(self::$instance[$con_name])) {
            self::$instance[$con_name] = Db::connect($config, $con_name);
        }

        return self::$instance[$con_name];
    }
}
