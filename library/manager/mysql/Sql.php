<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 16:32
 */

namespace tpr\db\manager\mysql;

use http\Exception\InvalidArgumentException;

class Sql
{
    const DATATYPE     = 'datatype';
    const DB_CREATE    = 'db.create';
    const DB_DELETE    = 'db.delete';
    const TABLE_CREATE = 'table.create';
    const COLUMN_ADD   = 'column.add';

    public static function getSql($opt, $data = [])
    {
        $check = self::checkElement($opt, $data);
        if ($check !== true) {
            throw new InvalidArgumentException("`" . $check . "` not exist!");
        }
        $sql = self::getSqlTemplate($opt);

        foreach ($data as $k => $v) {
            $key = '{' . $k . '}';
            str_replace('`', '', $v);
            if (strpos($sql, $key) != false) {
                $sql = str_replace($key, $v, $sql);
            }
        }

        return $sql;
    }

    public static function getSqlTemplate($opt)
    {
        return isset(self::$sql[$opt]) ? self::$sql[$opt] : "";
    }

    public static function checkElement($opt, $data = [])
    {
        $sql = self::getSqlTemplate($opt);

        $need_key = [];
        while (!empty($sql) && strpos($sql, '{') !== false) {
            $left  = strpos($sql, '{');
            $right = strpos($sql, '}');
            $str   = mb_substr($sql, $left + 1, $right - $left - 1);
            array_push($need_key, $str);
            $sql = mb_substr($sql, $right + 1);
        }

        foreach ($need_key as $key) {
            if (!is_null($data[$key])) {
                return $key;
            }
        }
        return true;
    }

    public static $sql = [
        'db.create'     => "CREATE DATABASE IF NOT EXISTS {name} DEFAULT CHARACTER SET {charset} COLLATE {collate}",
        'db.delete'     => "DROP DATABASE IF EXISTS {name}",
        'db.show'       => "SHOW DATABASES",
        'table.create'  => "CREATE TABLE {table_name} () ENGINE={engine} AUTO_INCREMENT={auto_increment} DEFAULT CHARSET={charset}",
        'table.delete'  => "DROP TABLE IF EXISTS {table_name}",
        'index.delete'  => "ALTER TABLE {table_name} DROP INDEX {index_name}",
        'column.add'    => "ALTER TABLE {table_name} ADD {column_name} {datatype}",
        'column.delete' => "ALTER TABLE {table_name} DROP COLUMN {column_name}",
        'column.update' => "ALTER TABLE {table_name} MODIFY COLUMN {column_name} {datatype}",

        'datatype' => "CHARACTER SET {charset} COLLATE {collate}"
    ];
}