<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/4/10 10:16
 */

namespace tpr\db;

use tpr\db\core\Connection;

class Db
{
    /**
     * @var array 数据库连接实例
     */
    private static $instance = [];

    /**
     * @var int 查询次数
     */
    public static $queryTimes = 0;

    /**
     * @var int 执行次数
     */
    public static $executeTimes = 0;

    /**
     * @param array $config
     * @param string $name
     * @return Connection
     */
    public static function connect($config = [], $name = 'default')
    {
        if(is_string($config) || (isset($config['dsn']) && !empty($config['dsn']) )){
            $config = self::parseDsn($config);
        }
        $options = array_merge(DbOption::$defaultConfig, $config);
        if (empty($options['type'])) {
            throw new \InvalidArgumentException('Undefined db type');
        }
        $class = false !== strpos($options['type'], '\\') ? $options['type'] : '\\tpr\\db\\driver\\' . ucwords($options['type']);
        self::$instance[$name] = new $class($options);
        return self::$instance[$name];
    }

    public static function model($name = "default")
    {
        if (!isset(self::$instance[$name])) {
            throw new \InvalidArgumentException('Undefined connector');
        }

        return self::$instance[$name];
    }

    public static function clear($name = null)
    {
        if(is_null($name)){
            self::$instance = null;
        }else{
            unset(self::$instance[$name]);
        }
    }

    /**
     * DSN解析
     * 格式： mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2#utf8
     * @param $dsnStr
     * @return array
     */
    private static function parseDsn($dsnStr)
    {
        $info = parse_url($dsnStr);
        if (!$info) {
            return [];
        }
        $dsn = [
            'type' => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'hostname' => isset($info['host']) ? $info['host'] : '',
            'hostport' => isset($info['port']) ? $info['port'] : '',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? $info['fragment'] : 'utf8',
        ];

        if (isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }
        return $dsn;
    }
}