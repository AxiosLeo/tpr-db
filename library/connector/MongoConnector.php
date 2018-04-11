<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/4/10 16:19
 */

namespace tpr\db\connector;

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use tpr\db\Db;
use tpr\db\exception\Exception;
use tpr\db\query\MongoQuery;

class MongoConnector
{

    protected $dbName = ''; // dbName
    /** @var string 当前SQL指令 */
    protected $queryStr = '';
    // 查询数据类型
    protected $typeMap = 'array';

    /**
     * @var \MongoDB\Driver\Manager
     */
    protected $mongo; // MongoDb Object
    /**
     * @var \MongoDB\Driver\Cursor
     */
    protected $cursor; // MongoCursor Object

    // 监听回调
    protected static $event = [];
    /** @var \PDO[] 数据库连接ID 支持多个连接 */
    protected $links = [];
    /** @var \PDO 当前连接ID */
    protected $linkID;
    protected $linkRead;
    protected $linkWrite;

    // 返回或者影响记录数
    protected $numRows = 0;
    // 错误信息
    protected $error = '';
    // 查询对象
    protected $query = [];
    // 查询参数
    protected $options = [];
    // 数据库连接参数配置
    protected $config = [
        // 数据库类型
        'type'            => '',
        // 服务器地址
        'hostname'        => '',
        // 数据库名
        'database'        => '',
        // 是否是复制集
        'is_replica_set'  => false,
        // 用户名
        'username'        => '',
        // 密码
        'password'        => '',
        // 端口
        'hostport'        => '',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 主键名
        'pk'              => '_id',
        // 主键类型
        'pk_type'         => 'ObjectID',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库调试模式
        'debug'           => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'     => false,
        // 读写分离后 主服务器数量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格检查字段是否存在
        'fields_strict'   => true,
        // 数据集返回类型
        'resultset_type'  => 'array',
        // 自动写入时间戳字段
        'auto_timestamp'  => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
        // 是否_id转换为id
        'pk_convert_id'   => false,
        // typeMap
        'type_map'        => ['root' => 'array', 'document' => 'array'],
        // Query对象
        'query'           => '\\tpr\\db\\query\\MongoQuery',
    ];

    /**
     * 架构函数 读取数据库配置信息
     * Connection constructor.
     * @access public
     * @param array $config 数据库配置数组
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        if (!class_exists('\MongoDB\Driver\Manager')) {
            throw new Exception('require mongodb > 1.0');
        }
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 连接数据库方法
     * @access public
     * @param array $config 连接参数
     * @param int $linkNum 连接序号
     * @return \PDO
     */
    public function connect(array $config = [], $linkNum = 0)
    {
        if (!isset($this->links[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }
            $this->dbName  = $config['database'];
            $this->typeMap = $config['type_map'];

            if ($config['pk_convert_id'] && '_id' == $config['pk']) {
                $this->config['pk'] = 'id';
            }
            $host = 'mongodb://' . ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '') . '/' . ($config['database'] ? "{$config['database']}" : '');

            $this->links[$linkNum] = new Manager($host, $this->config['params']);
        }
        return $this->links[$linkNum];
    }

    /**
     * 指定当前使用的查询对象
     * @access public
     * @param $query
     * @param string $model 查询对象
     * @return $this
     */
    public function setQuery($query, $model = 'db')
    {
        $this->query[$model] = $query;
        return $this;
    }

    /**
     * 创建指定模型的查询对象
     * @access public
     * @param string $model 模型类名称
     * @param string $queryClass 查询对象类名
     * @return MongoQuery
     */
    public function getQuery($model = 'db', $queryClass = '')
    {
        if (!isset($this->query[$model])) {
            $class               = $queryClass ?: $this->config['query'];

            $this->query[$model] = new $class($this, 'db' == $model ? '' : $model);
        }
        return $this->query[$model];
    }

    /**
     * 调用Query类的查询方法
     * @access public
     * @param string    $method 方法名称
     * @param array     $args 调用参数
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->getQuery(), $method], $args);
    }

    /**
     * 获取数据库的配置参数
     * @access public
     * @param string $config 配置名称
     * @return mixed
     */
    public function getConfig($config = '')
    {
        return $config ? $this->config[$config] : $this->config;
    }

    /**
     * 设置数据库的配置参数
     * @access public
     * @param string    $config 配置名称
     * @param mixed     $value 配置值
     * @return void
     */
    public function setConfig($config, $value)
    {
        $this->config[$config] = $value;
    }

    /**
     * 获取Mongo Manager对象
     * @access public
     * @return Manager|null
     */
    public function getMongo()
    {
        if (!$this->mongo) {
            return null;
        } else {
            return $this->mongo;
        }
    }

    /**
     * 设置/获取当前操作的database
     * @access public
     * @param null $db
     * @return string
     */
    public function db($db = null)
    {
        if (is_null($db)) {
            return $this->dbName;
        } else {
            $this->dbName = $db;
        }
        return null;
    }

    /**
     * 执行查询
     * @access public
     * @param string            $namespace 当前查询的collection
     * @param Query             $query 查询对象
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的数据集类型
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function query($namespace, Query $query, ReadPreference $readPreference = null, $class = false, $typeMap = null)
    {
        $this->initConnect(false);
        Db::$queryTimes++;

        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }
        if ($this->config['debug'] && !empty($this->queryStr)) {
            // 记录执行指令
            $this->queryStr = 'db' . strstr($namespace, '.') . '.' . $this->queryStr;
        }
        $this->cursor = $this->mongo->executeQuery($namespace, $query, $readPreference);
        return $this->getResult($class, $typeMap);
    }

    /**
     * 执行指令
     * @access public
     * @param Command           $command 指令
     * @param string            $dbName 当前数据库名
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的数据集类型
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function command(Command $command, $dbName = '', ReadPreference $readPreference = null, $class = false, $typeMap)
    {
        $this->initConnect(false);
        Db::$queryTimes++;
        $dbName = $dbName ?: $this->dbName;
        if ($this->config['debug'] && !empty($this->queryStr)) {
            $this->queryStr = 'db.' . $this->queryStr;
        }
        $this->cursor = $this->mongo->executeCommand($dbName, $command, $readPreference);
        return $this->getResult($class, $typeMap);

    }

    /**
     * 获得数据集
     * @access protected
     * @param bool|string       $class true 返回Mongo cursor对象 字符串用于指定返回的类名
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     */
    protected function getResult($class = '', $typeMap = null)
    {
        if (true === $class) {
            return $this->cursor;
        }
        // 设置结果数据类型
        if (is_null($typeMap)) {
            $typeMap = $this->typeMap;
        }
        $typeMap = is_string($typeMap) ? ['root' => $typeMap] : $typeMap;
        $this->cursor->setTypeMap($typeMap);

        // 获取数据集
        $result = $this->cursor->toArray();
        if ($this->getConfig('pk_convert_id')) {
            // 转换ObjectID 字段
            foreach ($result as &$data) {
                $this->convertObjectID($data);
            }
        }
        $this->numRows = count($result);

        return $result;
    }

    /**
     * ObjectID处理
     * @access public
     * @param array     $data
     * @return void
     */
    private function convertObjectID(&$data)
    {
        if (isset($data['_id'])) {
            $data['id'] = (string) $data['_id'];
            unset($data['_id']);
        }
    }

    /**
     * 执行写操作
     * @access public
     * @param string        $namespace
     * @param BulkWrite     $bulk
     * @param WriteConcern|null $writeConcern
     * @return \MongoDB\Driver\WriteResult
     */
    public function execute($namespace, BulkWrite $bulk, WriteConcern $writeConcern = null)
    {
        $this->initConnect(true);
        Db::$executeTimes++;
        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }
        if ($this->config['debug'] && !empty($this->queryStr)) {
            // 记录执行指令
            $this->queryStr = 'db' . strstr($namespace, '.') . '.' . $this->queryStr;
        }
        $writeResult = $this->mongo->executeBulkWrite($namespace, $bulk, $writeConcern);
        $this->numRows = $writeResult->getMatchedCount();
        return $writeResult;
    }

    /**
     * 数据库日志记录（仅供参考）
     * @access public
     * @param string $type 类型
     * @param mixed  $data 数据
     * @param array  $options 参数
     * @return void
     */
    public function log($type, $data, $options = [])
    {
        if (!$this->config['debug']) {
            return;
        }
        if (is_array($data)) {
            array_walk_recursive($data, function (&$value) {
                if ($value instanceof ObjectId) {
                    $value = $value->__toString();
                }
            });
        }
        switch (strtolower($type)) {
            case 'aggregate':
                $this->queryStr = 'runCommand(' . ($data ? json_encode($data) : '') . ');';
                break;
            case 'find':
                $this->queryStr = $type . '(' . ($data ? json_encode($data) : '') . ')';
                if (isset($options['sort'])) {
                    $this->queryStr .= '.sort(' . json_encode($options['sort']) . ')';
                }
                if (isset($options['limit'])) {
                    $this->queryStr .= '.limit(' . $options['limit'] . ')';
                }
                $this->queryStr .= ';';
                break;
            case 'insert':
            case 'remove':
                $this->queryStr = $type . '(' . ($data ? json_encode($data) : '') . ');';
                break;
            case 'update':
                $this->queryStr = $type . '(' . json_encode($options) . ',' . json_encode($data) . ');';
                break;
            case 'cmd':
                $this->queryStr = $data . '(' . json_encode($options) . ');';
                break;
        }
        $this->options = $options;
    }

    /**
     * 获取执行的指令
     * @access public
     * @return string
     */
    public function getQueryStr()
    {
        return $this->queryStr;
    }

    /**
     * 监听SQL执行
     * @access public
     * @param callable $callback 回调方法
     * @return void
     */
    public function listen($callback)
    {
        self::$event[] = $callback;
    }

    /**
     * 触发SQL事件
     * @access protected
     * @param string    $sql 语句
     * @param float     $runtime 运行时间
     * @param array     $options 参数
     * @return bool
     */
    protected function trigger($sql, $runtime, $options = [])
    {
        if (!empty(self::$event)) {
            foreach (self::$event as $callback) {
                if (is_callable($callback)) {
                    call_user_func_array($callback, [$sql, $runtime, $options]);
                }
            }
        }
        return null;
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free()
    {
        $this->cursor = null;
    }

    /**
     * 关闭数据库
     * @access public
     */
    public function close()
    {
        $this->mongo     = null;
        $this->cursor    = null;
        $this->linkRead  = null;
        $this->linkWrite = null;
        $this->links     = [];
    }

    /**
     * 初始化数据库连接
     * @access protected
     * @param boolean $master 是否主服务器
     * @return void
     */
    protected function initConnect($master = true)
    {
        if (!empty($this->config['deploy'])) {
            // 采用分布式数据库
            if ($master) {
                if (!$this->linkWrite) {
                    $this->linkWrite = $this->multiConnect(true);
                }
                $this->mongo = $this->linkWrite;
            } else {
                if (!$this->linkRead) {
                    $this->linkRead = $this->multiConnect(false);
                }
                $this->mongo = $this->linkRead;
            }
        } elseif (!$this->mongo) {
            // 默认单数据库
            $this->mongo = $this->connect();
        }
    }

    /**
     * 连接分布式服务器
     * @access protected
     * @param boolean $master 主服务器
     * @return \PDO|\MongoDB\Driver\Manager
     */
    protected function multiConnect($master = false)
    {
        $_config = [];
        // 分布式数据库配置解析
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $_config[$name] = explode(',', $this->config[$name]);
        }

        // 主服务器序号
        $m = floor(mt_rand(0, $this->config['master_num'] - 1));

        if ($this->config['rw_separate']) {
            // 主从式采用读写分离
            if ($master) // 主服务器写入
            {
                if ($this->config['is_replica_set']) {
                    return $this->replicaSetConnect();
                } else {
                    $r = $m;
                }
            } elseif (is_numeric($this->config['slave_no'])) {
                // 指定服务器读
                $r = $this->config['slave_no'];
            } else {
                // 读操作连接从服务器 每次随机连接的数据库
                $r = floor(mt_rand($this->config['master_num'], count($_config['hostname']) - 1));
            }
        } else {
            // 读写操作不区分服务器 每次随机连接的数据库
            $r = floor(mt_rand(0, count($_config['hostname']) - 1));
        }
        $dbConfig = [];
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $dbConfig[$name] = isset($_config[$name][$r]) ? $_config[$name][$r] : $_config[$name][0];
        }
        return $this->connect($dbConfig, $r);
    }

    /**
     * 创建基于复制集的连接
     * @return Manager
     */
    public function replicaSetConnect()
    {
        $this->dbName  = $this->config['database'];
        $this->typeMap = $this->config['type_map'];

        $this->config['params']['replicaSet'] = $this->config['database'];
        $manager                              = new Manager($this->buildUrl(), $this->config['params']);
        return $manager;
    }

    /**
     * 根据配置信息 生成适用于链接复制集的 URL
     * @return string
     */
    private function buildUrl()
    {
        $url      = 'mongodb://' . ($this->config['username'] ? "{$this->config['username']}" : '') . ($this->config['password'] ? ":{$this->config['password']}@" : '');
        $hostList = explode(',', $this->config['hostname']);
        $portList = explode(',', $this->config['hostport']);
        for ($i = 0; $i < count($hostList); $i++) {
            $url = $url . $hostList[$i] . ':' . $portList[0] . ',';
        }
        return rtrim($url, ",") . '/';
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 释放查询
        $this->free();

        // 关闭连接
        $this->close();
    }
}