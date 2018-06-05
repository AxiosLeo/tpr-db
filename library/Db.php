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
use tpr\db\core\Query;

/**
 * Class Db
 * @package tpr\db
 * @method Query table(string $table) static 指定数据表（含前缀）
 * @method Query name(string $name) static 指定数据表（不含前缀）
 * @method Query where(mixed $field, string $op = null, mixed $condition = null) static 查询条件
 * @method Query join(mixed $join, mixed $condition = null, string $type = 'INNER') static JOIN查询
 * @method Query union(mixed $union, boolean $all = false) static UNION查询
 * @method Query limit(mixed $offset, integer $length = null) static 查询LIMIT
 * @method Query order(mixed $field, string $order = null) static 查询ORDER
 * @method Query cache(mixed $key = null, integer $expire = null) static 设置查询缓存
 * @method mixed value(string $field) static 获取某个字段的值
 * @method array column(string $field, string $key = '') static 获取某个列的值
 * @method Query view(mixed $join, mixed $field = null, mixed $on = null, string $type = 'INNER') static 视图查询
 * @method mixed find(mixed $data = null) static 查询单个记录
 * @method mixed select(mixed $data = null) static 查询多个记录
 * @method integer insert(array $data, boolean $replace = false, boolean $getLastInsID = false, string $sequence = null) static 插入一条记录
 * @method integer insertGetId(array $data, boolean $replace = false, string $sequence = null) static 插入一条记录并返回自增ID
 * @method integer insertAll(array $dataSet) static 插入多条记录
 * @method integer update(array $data) static 更新记录
 * @method integer delete(mixed $data = null) static 删除记录
 * @method boolean chunk(integer $count, callable $callback, string $column = null) static 分块获取数据
 * @method mixed query(string $sql, array $bind = [], boolean $master = false, bool $pdo = false) static SQL查询
 * @method integer execute(string $sql, array $bind = [], boolean $fetch = false, boolean $getLastInsID = false, string $sequence = null) static SQL执行
 * @method mixed transaction(callable $callback) static 执行数据库事务
 * @method void startTrans() static 启动事务
 * @method void commit() static 用于非自动提交状态下面的查询提交
 * @method void rollback() static 事务回滚
 * @method boolean batchQuery(array $sqlArray) static 批处理执行SQL语句
 * @method string quote(string $str) static SQL指令安全过滤
 * @method string getLastInsID($sequence = null) static 获取最近插入的ID
 */
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
        $class = false !== strpos($options['type'], '\\') ? $options['type'] : '\\tpr\\db\\connector\\' . ucwords(strtolower($options['type'])) . "Connector";
        self::$instance[$name] = new $class($options);
        return self::$instance[$name];
    }

    /**
     * @param string $name
     * @return Connection
     */
    public static function model($name = "default")
    {
        if (!isset(self::$instance[$name])) {
            return null;
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