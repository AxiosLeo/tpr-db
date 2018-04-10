# TPR-DB
> 根据thinkphp的Db操作移植而来，做了很多改动，去掉了"查询缓冲"等跟tp耦合很强的功能。

> 可独立使用

## 使用示例

> 使用方法基本与tp的Db类相同，部分功能没有。

``` php
$database_config = [
    'database'=>'table_name'
];

$list = tpr\db\Db::connect($database_config)->name('table_name_without_prefix')->select();
dump($list);
```
