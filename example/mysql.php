<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-11-30 15:20
 */

use tpr\db\DbClient;
use tpr\db\DbFacade;

class Mysql extends DbFacade
{
    public static function __callStatic($method, $params)
    {
        $config = [
            'type'            => 'mysql',
            // 服务器地址
            'hostname'        => '127.0.0.1',
            // 数据库名
            'database'        => 'test',
            // 用户名
            'username'        => 'root',
            // 密码
            'password'        => 'root',
            // 端口
            'hostport'        => '3306',
            // 数据库表前缀
            'prefix'          => '',
        ];
        $Con    = DbClient::newCon('con_name', $config);
        return call_user_func_array([$Con, $method], $params);
    }
}