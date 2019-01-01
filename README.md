[![Latest Stable Version](https://poser.pugx.org/axios/tpr-db/v/stable)](https://packagist.org/packages/axios/tpr-db)
[![License](https://poser.pugx.org/axios/tpr-db/license)](https://packagist.org/packages/axios/tpr-db)

# TPR-DB
> 根据thinkphp的ORM移植而来，做了很多改动，去掉了"查询缓存"等跟tp耦合很强的功能。

> [packagist.org/packages/axios/tpr-db](https://packagist.org/packages/axios/tpr-db)

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

- Redis

  > [example/redis.php](https://github.com/AxiosCros/tpr-db/blob/master/example/redis.php)

## DbManager 数据库管理功能 (开发中)
> 使用示例 [manager_mysql.php](https://github.com/AxiosCros/tpr-db/blob/DbManager/example/manager_mysql.php)