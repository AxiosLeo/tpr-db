<?php

namespace tpr\db\manager\driver;

interface DriverInterface
{
    public function createDatabase($name);

    public function createTable();

    public function createField();
}
