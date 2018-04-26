# TPR-DB
> 根据thinkphp的ORM移植而来，做了很多改动，去掉了"查询缓冲"等跟tp耦合很强的功能。

## 特调
- 可独立使用

- 支持数据库多连接,利用这个特性可以轻松实现读写分离

- 在任何使用composer的php框架中都可以使用

## 目前支持的数据库

* Mysql  已支持
* MongoDb 已支持

## 安装

``` php
composer require axios/tpr-db:dev-master
```

## 使用示例

> 使用方法基本与tp的Db类相同，部分功能没有。

- Mysql

``` php
namespace tpr\db;

require_once __DIR__.'/../vendor/autoload.php';

$database_config = [
    "type"              => 'mongo',
    "hostname"          => '127.0.0.1',
    "database"          => 'test',
    "username"          => 'test',
    "password"          => '123456',
    "hostport"          => '27017',
];

$list = Db::connect($database_config)->name('test')->select();
dump($list);

$info = Db::model()->name('test')->where('id',1)->find();
dump($info);

$info = Db::name('test')->where('id',1)->find();
dump($info);

```

- Mongo

``` php
namespace tpr\db;

require_once __DIR__.'/../vendor/autoload.php';

$database_config = [
    "type"              => 'mysql',
    "hostname"          => '127.0.0.1',
    "database"          => 'test',
    "username"          => 'root',
    "password"          => 'root',
    "hostport"          => '3306',
];

$list = Db::connect($database_config)->name('test')->select();
dump($list);

$info = Db::model()->name('test')->where('id',1)->find();
dump($info);

$info = Db::name('test')->where('id',1)->find();
dump($info);

```