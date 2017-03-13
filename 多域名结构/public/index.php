<?php

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;



// 注册一个自动加载器
$loader = new Loader();

$loader->registerDirs(
    [
        "../app/controllers/www/",
        "../app/models/",
    ]
);

$loader->registerNamespaces(
    [
        "App\\Controllers\\Home" => "../app/controllers/www/",
    ]
);

$loader->register();



// 创建一个 DI
$di = new FactoryDefault();

// 设置视图组件
$di->set(
    "view",
    function () {
        $view = new View();
        $view->setViewsDir("../app/views/www/");
        return $view;
    }
);

$di->set(
    "dispatcher",
    function () {
        $dispatcher = new Dispatcher();

        $dispatcher->setDefaultNamespace(
            "App\\Controllers\\Home"
        );

        return $dispatcher;
    }
);

$di->set(
    "url",
    function () {
        $url = new UrlProvider();

        $url->setBaseUri("/");

        return $url;
    }
);

// 设置数据库服务
$di->set(
    "db",
    function () {
        return new DbAdapter(
            [
                "host"     => "127.0.0.1",
                "username" => "root",
                "password" => "123456",
                "dbname"   => "test",
            ]
        );
    }
);

$application = new Application($di);

try {
    // 处理请求
    $response = $application->handle();
    $response->send();
} catch (\Exception $e) {
    echo "Exception: ", $e->getMessage(), $e->getTraceAsString();
}