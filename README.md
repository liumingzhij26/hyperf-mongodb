# hyperf-mongodb

`composer require lmz/hyperf-mongodb`


## demo

配置文件路径：`config/autoload/mongodb.php`，可以参考 `publish` 目录下 `mongodb.php`

```php
<?php
return [

    'default' => [
        'host'     => [
            '127.0.0.1',
        ],
        'port'     => 27017,
        'database' => 'test',
        'username' => 'user_dev',
        'password' => '123456',
        'options'  => [
            'database' => 'admin',
        ],
        'pool'     => [
            'min_connections' => 10,
            'max_connections' => 100,
            'connect_timeout' => 10.0,
            'wait_timeout'    => 3.0,// 90
            'heartbeat'       => -1,
            'max_idle_time'   => (float)env('DB_MAX_IDLE_TIME', 60),
        ],
    ],


];

```

通过 `Mongodb\Client::class` ，调用，具体用法参考：https://docs.mongodb.com/ecosystem/drivers/php/

```php
$container->get(MongoDB\Client::class)->selectDatabase('database_name')->selectCollection('collection_name');
```

### 通过代理类使用

我们可以重写一个 `Course` 类并继承 `Hyperf\Mongodb\Mongodb` 类，修改 `poolName` 为上述的 `tf_course`，即可完成对连接池的切换，示例：

```php
/**
 * Class Course
 * @return Client|Course
 * @package App\Model\Mongo
 */
class Course extends Mongodb
{

    protected $mongoDbName = 'database_name';    // mongoDB 表名

    protected $poolName = 'course';// 连接池名称

    public function db()
    {
        $container = ApplicationContext::getContainer();
        /**
         * @var $mongodb Client
         */
        $mongodb = $container->get(self::class);
        return $mongodb->selectDatabase($this->mongoDbName);
    }

}

$container->get(Course::class)->db()->selectCollection('collection_name')->findOne([
    'id' => 1
])

```