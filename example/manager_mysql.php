<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-19 15:26
 */

namespace tpr\db\manager;

use tpr\db\DbManager;

// 数据库配置
$database_config = [
    "type"     => 'mysql',
    "hostname" => '127.0.0.1',
    "database" => 'api',
    "username" => 'root',
    "password" => 'root',
    "hostport" => '3306',
];

require_once __DIR__ . '/../vendor/autoload.php';

// 数据库管理实例，目前只支持mysql数据库操作
$DBM = DbManager::instance()->mysql('mysql.db_manager', $database_config);
// mysql.db_manager 是连接名，相同的名称获取到的是同一个driver
// 也就是第二次调用mysql方法时，可以不用设置$database_config，通过连接名直接获取driver

// 源数据库名称
$source_db = 'source_db_name';
// 目标数据库名称
$target_db = 'target_db_name';

// 判断源数据库是否存在
$is_exist = $DBM->dbExist($source_db);
if (!$is_exist) {
    echo $source_db . "数据库不存在";
    die();
}

// 创建目标数据库
$DBM->database()->create($target_db)->exec();

// 获取源数据库的数据表列表
$tables = $DBM->database($source_db)->getTableList();

// 遍历所有表，并同步源数据库数据表中的数据至目标数据库中同名数据表
foreach ($tables as $table) {
    // 数据表实例
    $Table = $DBM->database($target_db)->table($table);
    // 删除已有表
    $Table->delete()->exec();
    // 创建数据表
    $Table->create($source_db . '.' . $table)->exec();
    // 同步数据
    $Table->sysData($source_db, $table)->exec();
    unset($Table);
}

// 将源数据库的所有数据导出为sql文件
// mode=0 单文件存储
// mode=1 数据库创建一个文件，表创建及表数据插入在同一个文件
// mode=2 数据库创建一个文件，表创建一个文件，表数据插入在以表名为目录名的目录中有多个文件，每个文件存储limit行insert操作
$DBM->database($source_db)->saveAllData(__DIR__ . '/db_sql', 2, 1000);
