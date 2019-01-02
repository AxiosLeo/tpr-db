<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-21 11:29
 */

namespace tpr\db\manager\mysql;

class Operation
{
    const DATATYPE       = 'datatype';
    const DB_CREATE      = 'db.create';
    const DB_DELETE      = 'db.delete';
    const DB_EXIST       = 'db.exist';
    const TABLE_CREATE   = 'table.create';
    const TABLE_DELETE   = 'table.delete';
    const TABLE_SYS      = 'table.sys';
    const TABLE_SYS_DATA = 'table.sys_data';
    const TABLE_SHOW     = 'table.show';
    const COLUMN_ADD     = 'column.add';
    const FETCH_ALL      = 'fetch.all';
}