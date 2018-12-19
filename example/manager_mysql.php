<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-19 15:26
 */
namespace tpr\db\manager;

use tpr\db\DbManager;

$database_config = [
    "type"     => 'mysql',
    "hostname" => '127.0.0.1',
    "database" => 'api',
    "username" => 'root',
    "password" => 'root',
    "hostport" => '3306',
];

require_once __DIR__ . '/../vendor/autoload.php';

$DbM = DbManager::instance()->mysql('test', $database_config);
$DbM->database()
    ->table('test')
    ->column('test')
    ->add();

print_r($DbM->viewSql());