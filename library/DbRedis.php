<?php

namespace tpr\db;

use Redis;
use RedisCluster;
use tpr\db\redis\KV;

class DbRedis
{
    private static $defaultConfig = [
        'single'  => [
            'host'     => '127.0.0.1',
            'auth'     => '',
            'port'     => '6379',
            'prefix'   => 'redis:',
            'timeout'  => 60,
            'database' => [
                'default' => 0,
            ],
        ],
        'cluster' => [
            'cluster_name' => null,
            'hosts'        => [
                '127.0.0.1:6379',
            ],
            'auth'         => '',
            'prefix'       => 'redis:',
            'timeout'      => 1.5,
            'read_timeout' => 1.5,
            'persistent'   => true,
            'database'     => [
                'default' => 0,
            ],
        ],
    ];

    private static $instance = [];

    /**
     * @var Redis|RedisCluster
     */
    private $redis;

    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
        $is_cluster   = isset($this->config['cluster']) ? $this->config['cluster'] : false;
        if ($is_cluster) {
            $this->redis = new RedisCluster(
                $config['cluster_name'],
                $config['seeds'],
                $config['timeout'],
                $config['read_timeout'],
                $config['persistent']
            );
        } else {
            $this->redis = new Redis();
            $this->redis->connect(
                $config['host'],
                $config['port'],
                $config['timeout']
            );
        }

        if (!empty($config['auth'])) {
            $this->redis->auth($config['auth']);
        }
        $this->redis->setOption(Redis::OPT_PREFIX, $config['prefix']);
    }

    public function __destruct()
    {
        $this->redis()->close();
    }

    /**
     * @param string $name
     * @param array  $config
     *
     * @return self
     */
    public static function init($name = '', $config = [])
    {
        if (empty($name)) {
            $name = 'redis.default';
        }
        if (isset(self::$instance[$name])) {
            return self::$instance[$name];
        }
        $hosts = isset($config['hosts']) ? $config['hosts'] : [];

        $is_cluster = !empty($hosts);

        $default_config = $is_cluster ? self::$defaultConfig['cluster'] : self::$defaultConfig['single'];

        $config = array_merge($default_config, $config);

        self::$instance[$name] = self::instance($config);

        return self::$instance[$name];
    }

    public static function clear($name = null)
    {
        if (null === $name) {
            self::$instance = [];
        } elseif (isset(self::$instance[$name])) {
            unset(self::$instance[$name]);
        }
    }

    /**
     * @param array $config
     *
     * @return DbRedis
     */
    public static function instance($config = [])
    {
        return new self($config);
    }

    public function redis()
    {
        return $this->redis;
    }

    public function kv($key)
    {
        return KV::key($key, $this->redis);
    }

    public function counter($key, $init = 0, $expire = 0)
    {
        if (empty($expire)) {
            $this->redis()->set($key, $init);
        } else {
            $this->redis()->psetex($key, $expire, $init);
        }

        return $init;
    }

    public function countNumber($key)
    {
        if (!$this->redis()->exists($key)) {
            return false;
        }

        return $this->redis()->get($key);
    }

    /**
     * @desc 进行计数
     *
     * @param $key
     *
     * @return bool|int
     */
    public function count($key)
    {
        if (!$this->redis()->exists($key)) {
            return false;
        }

        return $this->redis()->incr($key);
    }

    public function setsMembers($key)
    {
        $size    = $this->redis()->sCard($key);
        $members = [];
        for ($i = 0; $i < $size; ++$i) {
            $members[$i] = $this->redis()->sPop($key);
        }
        foreach ($members as $m) {
            $this->redis()->sAdd($key, $m);
        }

        return $members;
    }

    public function setArray($key, $array, $ttl = 0)
    {
        if ($ttl) {
            return $this->redis()->set($key, $this->formatArray($array), ['ex' => $ttl]);
        }

        return $this->redis()->set($key, $this->formatArray($array));
    }

    public function getArray($key)
    {
        if (!$this->redis()->exists($key)) {
            return false;
        }

        return $this->unFormatArray($this->redis()->get($key));
    }

    private function formatArray($array)
    {
        return base64_encode(@serialize($array));
    }

    private function unFormatArray($data)
    {
        return @unserialize(base64_decode($data));
    }
}
