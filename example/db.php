<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-11-30 15:20
 */

namespace tpr\db\example;

use tpr\db\DbClient;
use tpr\db\DbFacade;

class Mysql extends DbFacade
{
    private static $Con;

    public static function __callStatic($method, $params)
    {
        if (is_null(self::$Con)) {
            $config    = [
                'type'     => 'mysql', // mongo | pgsql | mysql
                // 服务器地址
                'hostname' => '127.0.0.1',
                // 数据库名
                'database' => 'test',
                // 用户名
                'username' => 'root',
                // 密码
                'password' => 'root',
                // 端口
                'hostport' => '3306',
                // 数据库表前缀
                'prefix'   => '',
            ];
            self::$Con = DbClient::newCon('con_name', $config);
        }
        return call_user_func_array([self::$Con, $method], $params);
    }
}