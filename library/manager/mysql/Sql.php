<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 16:32
 */

namespace tpr\db\manager\mysql;

class Sql
{
    public static function getSql($opt = '', $data = [])
    {
        $sql = isset(self::$sql[$opt]) ? self::$sql[$opt] : "";

        foreach ($data as $k => $v) {
            $key = '{' . $k . '}';
            str_replace('`', '', $v);
            if (strpos($sql, $key) != false) {
                $sql = str_replace($key, $v, $sql);
            }
        }

        return $sql;
    }

    public static $sql = [
        'db.create'     => "CREATE DATABASE IF NOT EXISTS `{name}` DEFAULT CHARACTER SET {charset} COLLATE {collate}",
        'db.delete'     => "DROP DATABASE IF EXISTS `{name}`",
        'db.show'       => "SHOW DATABASES",
        'table.create'  => "CREATE TABLE `{table_name}` () ENGINE={engine} AUTO_INCREMENT={auto_increment} DEFAULT CHARSET={charset}",
        'table.delete'  => "DROP TABLE IF EXISTS `{table_name}`",
        'index.delete'  => "ALTER TABLE `{db_name}`.`{table_name}` DROP INDEX `{index_name}`",
        'column.add'    => "ALTER TABLE `{db_name}`.`{table_name}` ADD `{column_name}` {datatype}",
        'column.delete' => "ALTER TABLE `{db_name}`.`{table_name}` DROP COLUMN `{column_name}`",
        'column.update' => "ALTER TABLE `{db_name}`.`{table_name}` MODIFY COLUMN `{column_name}` {datatype}"
    ];
}