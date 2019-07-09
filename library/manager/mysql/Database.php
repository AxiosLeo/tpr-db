<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 16:38
 */

namespace tpr\db\manager\mysql;

use think\exception\PDOException;
use tpr\db\core\FilesOpt;
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
            'name' => $this->dbName(),
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
            'name'    => null === $name ? $this->dbName() : $name,
            'charset' => $this->charset,
            'collate' => $this->charset . $this->collate,
        ];
        $this->operation = Operation::DB_CREATE;

        return $this;
    }

    public function delete()
    {
        $this->sql_data  = [
            'name' => $this->formatDbName($this->dbName()),
        ];
        $this->operation = Operation::DB_DELETE;

        return $this;
    }

    public function outputStructure($path)
    {
        $path   = FilesOpt::filePath($path);
        $tables = $this->getTableList();
        foreach ($tables as $table) {
            $table_name = '`' . $table . '`';
            $filename   = $path . $table . '.sql';
            if (file_exists($filename)) {
                @unlink($filename);
            }
            FilesOpt::saveFile($filename, $this->getSql($this->query->query('SHOW CREATE TABLE ' . $table_name)) . ';');
        }

        return $this;
    }

    public function outputAllData($path, $tables = '', $limit = 100)
    {
        $path = FilesOpt::filePath($path);

        if (!empty($tables)) {
            if (!\is_array($tables)) {
                $tables = explode(',', $tables);
            }
        } else {
            $tables = $this->getTableList();
        }

        $params = [
            'output_path'     => $path,
            'tables'          => $tables,
            'page_limit'      => $limit,
            'database_config' => $this->getOptions(),
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
                'table'      => $table,
                'total'      => $total,
                'page_total' => $page_total,
            ];
            DbOptHook::listen('output_table_data_begin', $params);
            unset($params);
            while ($total > 0) {
                ++$m;
                $filename_data = FilesOpt::filePath($path . $table) . 'page_' . $m . '.sql';
                if (file_exists($filename_data)) {
                    @unlink($filename_data);
                }
                $data = $this->query->table($table)->page($m)->limit($limit)->select();
                foreach ($data as $d) {
                    FilesOpt::saveFile($filename_data, $this->buildDataSql($table_name, $d));
                }
                $total  = $total - $limit;
                $params = [
                    'data_file_path' => $filename_data,
                    'page_curr'      => $m,
                    'page_total'     => $page_total,
                ];
                DbOptHook::listen('output_table_data_per_page', $params);
                unset($filename_data, $data, $params);
            }
        }

        return $this;
    }

    public function importData($data_path)
    {
        $data_path      = FilesOpt::filePath($data_path);
        $sql_files_list = FilesOpt::searchFile($data_path, ['sql']);

        foreach ($sql_files_list as $sql_file) {
            $table_name  = basename($sql_file, '.sql');
            $table_exist = $this->tableExist($this->db_name, $table_name);
            if (!$table_exist) {
                $sql = file_get_contents($sql_file);
                $sql = str_replace(["\r\n", "\n"], '', $sql);
                if (!empty($sql)) {
                    try {
                        $this->getQuery()->query($sql);
                    } catch (\PDOException $e) {
                        $params = ['exception' => $e, 'sql' => $sql];
                        DbOptHook::listen('throw_exception', $params);
                    } catch (PDOException $e) {
                        $params = ['exception' => $e, 'sql' => $sql];
                        DbOptHook::listen('throw_exception', $params);
                    }
                }
            }

            // 同步数据
            $table_data_files = FilesOpt::searchFile($data_path . $table_name . '/');
            $this->getQuery()->query("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' ");
            if (!empty($table_data_files)) {
                $table_data_files = $this->sortFiles($table_data_files);
                $total            = \count($table_data_files);
                $params           = ['table_name' => $table_name, 'table_data_files' => $table_data_files, 'total' => $total];
                DbOptHook::listen('import_table_data_begin', $params);
                $n = 0;
                foreach ($table_data_files as $page => $data_file) {
                    $sql      = file_get_contents($data_file);
                    $sqlArray = explode('INSERT', $sql);
                    foreach ($sqlArray as $sql) {
                        $sql = str_replace(["\r\n", "\n"], '', $sql);
                        if (!empty($sql)) {
                            $sql = 'INSERT' . $sql;

                            try {
                                $this->getQuery()->query($sql);
                            } catch (\Exception $e) {
                                $params = ['exception' => $e, 'sql' => $sql];
                                DbOptHook::listen('throw_exception', $params);
                            }
                        }
                    }
                    $params = ['table_name' => $table_name, 'file_path' => $data_file, 'page' => $n, 'total' => $total];
                    DbOptHook::listen('import_table_data_page', $params);
                    unset($sql, $sqlArray);
                }
                $params = [];
                DbOptHook::listen('import_table_data_end', $params);
            }
            unset($table_data_files);
        }
    }

    private function sortFiles($table_data_files, $ext = '.sql')
    {
        $list = [];
        foreach ($table_data_files as $data_file) {
            $filename        = basename($data_file, $ext);
            list($tmp, $key) = explode('_', $filename);
            $list[$key]      = $data_file;
            unset($tmp);
        }
        ksort($list);

        return array_values($list);
    }

    private function getSql($result)
    {
        foreach ($result as $r) {
            $n = 0;
            foreach ($r as $t) {
                ++$n;
                if (2 == $n) {
                    return $t;
                }
            }
        }

        return '';
    }

    private function buildDataSql($table, $data)
    {
        $values = '';
        $n      = 0;
        foreach ($data as $d) {
            if ($n > 0) {
                $values .= ',';
            }
            if (null !== $d) {
                $tmp    = false !== strpos($d, "'") ? '"' . addslashes($d) . '"' : "'" . addslashes($d) . "'";
                $values .= $tmp;
            } else {
                $values .= 'null';
            }
            ++$n;
        }

        return Sql::getSql('insert.data', [
            'table_name' => $table,
            'values'     => $values,
        ]);
    }
}
