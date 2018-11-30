# TPR-DB
> 根据thinkphp的ORM移植而来，做了很多改动，去掉了"查询缓存"等跟tp耦合很强的功能。

## 特调
- 独立，在任何使用composer的php框架中都可以使用

- 支持数据库多连接,利用这个特性可以轻松实现读写分离

## 目前支持的数据库

* `Mysql`   需要安装pdo扩展
* `MongoDb`  需要安装mongodb扩展
* `Redis`  需要安装redis扩展

## 安装

``` php
composer require axios/tpr-db
```

## 使用示例

> 使用方法基本与tp的Db类相同，部分功能没有。

- Mysql | PGSql | MongoDB

  > [example/db.php](https://github.com/AxiosCros/tpr-db/blob/master/example/db.php)

## 自定义连接器示例
``` php
<?php
use tpr\db\DbClient;
use tpr\db\DbFacade;

class Mysql extends DbFacade
{
    public static function __callStatic($method, $params)
    {
        $config = [
            'type'            => 'mysql',
            // 服务器地址
            'hostname'        => '127.0.0.1',
            // 数据库名
            'database'        => 'test',
            // 用户名
            'username'        => 'root',
            // 密码
            'password'        => 'root',
            // 端口
            'hostport'        => '3306',
            // 数据库表前缀
            'prefix'          => '',
        ];
        $Con    = DbClient::newCon('con_name', $config);
        return call_user_func_array([$Con, $method], $params);
    }
}
```