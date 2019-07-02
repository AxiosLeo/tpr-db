<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018-12-18 14:54
 */

namespace tpr\db\manager\driver;

interface DriverInterface
{
    public function createDatabase($name);

    public function createTable();

    public function createField();
}