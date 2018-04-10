# TPR-DB
> 根据thinkphp的ORM移植而来，做了很多改动，去掉了"查询缓冲"等跟tp耦合很强的功能。

> 可独立使用

> 支持数据库多连接

## 目前支持的数据库

* Mysql  已支持
* MongoDb 开发中

## 使用示例

> 使用方法基本与tp的Db类相同，部分功能没有。

``` php
namespace tpr\db;

require_once __DIR__.'/../vendor/autoload.php';

$database_config = [
    'database'=>'test'
];

$list = Db::connect($database_config)->name('test')->select();
dump($list);

$info = Db::model()->name('test')->where('id',1)->find();
dump($info);

$info = Db::name('test')->where('id',1)->find();
dump($info);
```
