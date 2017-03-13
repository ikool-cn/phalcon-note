<?php

//http://docs.phalconphp.com/zh/latest/reference/cache.html
任选下面一种。

$di->set('modelsCache',function(){
    //Cache data for one day by default(86400s=1day)
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        'lifetime'=>86400,
    ));

    //(1)Memcache client settings
    $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
        "host" => "127.0.0.1",
        "port" => "11211"
    ));

    //(2)Memcached client settings
    $cache = new \Phalcon\Cache\Backend\Libmemcached($frontCache,array(
        'servers'=>array(
            array(
                'host'=>'192.168.1.100',
                'port'=>11211,
                'weight'=>2
            ),
            array(
                'host'=>'192.168.1.101',
                'port'=>11211,
                'weight'=>1
            ),
        ),
        'client'=>array(
            Memcached::OPT_HASH => Memcached::HASH_MD5,
            Memcached::OPT_PREFIX_KEY => 'prefix.',
        )
    ));

    //(3)File cache settings
    $cache = new Phalcon\Cache\Backend\File($frontCache, array(
        "cacheDir" => "../app/cache/"
    ));

    //(4)APC cache
    $cache = new Phalcon\Cache\Backend\Apc($frontCache, array(
        'prefix' => 'cache'
    ));

    //(5)XCache
    $cache = new Phalcon\Cache\Backend\XCache($frontCache, array(
        'prefix' => 'cache'
    ));

    //(6)Create a MongoDB cache
    $cache = new Phalcon\Cache\Backend\Mongo($frontCache, array(
        'server' => "mongodb://localhost",
        'db' => 'caches',
        'collection' => 'images'
    ));

    return $cache;
});


//然后我们使用模型进行查询的时候就可以缓存了，注意这里只能缓存查询，元数据查询还会继续

$user = Users::find(array('userid = 27', 'cache' => array('key' => 'index/index', 'lifetime' => 300)));

//或者

$query = $this->modelsManager->createQuery($phql);
$query->cache(array(
    "key" => "cars-by-name",
    "lifetime" => 300
));


//数据库日志
2 Connect	root@localhost on test_db
2 Query	SELECT IF(COUNT(*)>0, 1 , 0) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME`='users'
2 Query	DESCRIBE `users`
2 Query	SELECT `users`.`userid`, `users`.`name`, `users`.`email`, `users`.`login_times` FROM `users` WHERE `users`.`userid` = 27
2 Quit	
3 Connect	root@localhost on test_db
3 Query	SELECT IF(COUNT(*)>0, 1 , 0) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME`='users'
3 Query	DESCRIBE `users`
3 Quit	