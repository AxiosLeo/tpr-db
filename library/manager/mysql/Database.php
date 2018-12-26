<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 16:38
 */

namespace tpr\db\manager\mysql;

use tpr\db\DbOptHook;
use tpr\db\manager\driver\Mysql;

class Database extends Mysql
{
    public function setCharSet($charset = null)
    {
        $this->charset = $charset;
        return $this;
    }

    public function setCollate($collate)
    {
        $this->collate = $collate;
        return $this;
    }

    public function table($table_name)
    {
        $Table = new Table();
        $Table->setTableName($table_name);
        $Table->dbName($this->dbName());
        return $Table;
    }

    public function getTableList()
    {
        $sql    = Sql::getSql(Operation::TABLE_SHOW, [
            "name" => $this->dbName()
        ]);
        $tables = $this->execSql($sql);
        $list   = [];
        foreach ($tables as $t) {
            foreach ($t as $k => $v) {
                array_push($list, $v);
            }
        }
        return $list;
    }

    public function create($name = null)
    {
        $this->sql_data  = [
            'name'    => is_null($name) ? $this->dbName() : $name,
            'charset' => $this->charset,
            'collate' => $this->charset . $this->collate
        ];
        $this->operation = Operation::DB_CREATE;
        return $this;
    }

    public function delete()
    {
        $this->sql_data  = [
            'name' => $this->formatDbName($this->dbName())
        ];
        $this->operation = Operation::DB_DELETE;
        return $this;
    }

    public function outputStructure($path)
    {
        $path = $this->filePath($path);
        $tables = $this->getTableList();
        foreach ($tables as $table) {
            $table_name = '`' . $table . '`';
            $filename = $path . DIRECTORY_SEPARATOR . $table . '.sql';
            if (file_exists($filename)) {
                @unlink($filename);
            }
            $this->saveFile($filename, $this->getSql($this->query->query("SHOW CREATE TABLE " . $table_name)) . ';');
        }
        return $this;
    }

    public function outputAllData($path, $tables = "", $limit = 100)
    {
        $path = $this->filePath($path);

        if (!empty($tables)) {
            if (!is_array($tables)) {
                $tables = explode(',', $tables);
            }
        } else {
            $tables = $this->getTableList();
        }

        $params = [
            "output_path"     => $path,
            "tables"          => $tables,
            "page_limit"      => $limit,
            "database_config" => $this->getOptions()
        ];
        DbOptHook::listen('output_all_data', $params);
        unset($params);

        foreach ($tables as $table) {
            // insert data sql
            $total      = $this->query->table($table)->count();
            $m          = 0;
            $table_name = '`' . $table . '`';
            $page_total = ceil($total / $limit);
            $params     = [
                "table"      => $table,
                "total"      => $total,
                "page_total" => $page_total
            ];
            DbOptHook::listen('output_table_data_begin', $params);
            unset($params);
            while ($total > 0) {
                $m++;
                $filename_data = $this->filePath($path . $table) . "page_" . $m . '.sql';
                if (file_exists($filename_data)) {
                    @unlink($filename_data);
                }
                $data = $this->query->table($table)->page($m)->limit($limit)->select();
                foreach ($data as $d) {
                    $this->saveFile($filename_data, $this->buildDataSql($table_name, $d));
                }
                $total  = $total - $limit;
                $params = [
                    "data_file_path" => $filename_data,
                    "page_curr"      => $m,
                    "page_total"     => $page_total
                ];
                DbOptHook::listen('output_table_data_per_page', $params);
                unset($filename_data);
                unset($data);
                unset($params);
            }
        }
        return $this;
    }

    private function filePath($path)
    {
        $path = substr($path, -1) != '/' ? $path . '/' : $path;
        if (!file_exists($path)) {
            if (!mkdir($path, 0700, true)) {
                return null;
            }
        }
        return $path;
    }

    private function saveFile($filename, $sql, $blank = 0)
    {
        $fp = fopen($filename, 'a+');
        if (flock($fp, LOCK_EX)) {
            while ($blank > 0) {
                fwrite($fp, "\r\n");
                $blank = $blank - 1;
            }
            fwrite($fp, $sql . "\r\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    private function getSql($result)
    {
        foreach ($result as $r) {
            $n = 0;
            foreach ($r as $t) {
                $n++;
                if ($n == 2) {
                    return $t;
                }
            }
        }
        return "";
    }

    private function buildDataSql($table, $data)
    {
        $values = "";
        $n      = 0;
        foreach ($data as $d) {
            if ($n > 0) {
                $values .= ",";
            }
            if (!is_null($d)) {
                $tmp    = strpos($d, "'") !== false ? '"' . addslashes($d) . '"' : "'" . addslashes($d) . "'";
                $values .= $tmp;
            } else {
                $values .= 'null';
            }
            $n++;
        }

        return Sql::getSql('insert.data', [
            'table_name' => $table,
            'values'     => $values
        ]);
    }
}