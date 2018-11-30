<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-11-30 15:11
 */

namespace tpr\db\redis;

/**
 * Class KV
 * @package tpr\db\redis
 * @method int append($value)
 * @method int bitCount()
 */
class KV
{
    /**
     * @param \Redis|\RedisCluster $redis
     * @param string               $key
     *
     * @return self
     */
    public static function key($key, $redis = null)
    {
        return new self($key, $redis);
    }

    /**
     * @var \Redis|\RedisCluster
     */
    private $redis;

    private $key;

    public function __construct($key, $redis)
    {
        $this->key   = $key;
        $this->redis = $redis;
    }

    public function set($value, $timeout = null)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return $this->redis->set($this->key, $value, $timeout);
    }

    public function get()
    {
        $data = $this->redis->get($this->key);
        $tmp  = @json_decode($data, true);
        if (is_array($tmp)) {
            $data = $tmp;
        }
        return $data;
    }

    public function exist()
    {
        return $this->redis->exists($this->key);
    }

    public function del()
    {
        return $this->redis->del($this->key);
    }

    public function ttl($micro = false)
    {
        return $micro ? $this->redis->pttl($this->key) : $this->redis->ttl($this->key);
    }

    public function __call($name, $arguments)
    {
        array_unshift($arguments, $this->key);
        return call_user_func_array([$this->redis, $name], $arguments);
    }
}