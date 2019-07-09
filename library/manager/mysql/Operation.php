<?php

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
    const TABLE_EXIST    = 'table.exist';
    const COLUMN_ADD     = 'column.add';
    const FETCH_ALL      = 'fetch.all';
}
