<?php

use tpr\db\DbRedis;

$single_config = [
    'host'     => '127.0.0.1',
    'auth'     => '',
    'port'     => '6379',
    'prefix'   => 'redis:',
    'timeout'  => 60,
    'database' => [
        'default' => 0,
    ],
];

$cluster_config = [
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
];

$redisClient   = DbRedis::init('con_name', $single_config);
//$redisClient   = DbRedis::init('con_name', $cluster_config);
$redisInstance = $redisClient->redis();
$redisInstance->info();

//kv opt
$data = ['test_data'];
$redisClient->kv('key')->set($data);
$redisClient->kv('key')->exist();
$redisClient->kv('key')->get();
