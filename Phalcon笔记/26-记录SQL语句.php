<?php
当使用高层次的抽象组件，比如 Phalcon\Mvc\Model 访问数据库时，很难理解这些语句最终发送到数据库时是什么样的。 
Phalcon\Mvc\Model 内部由 Phalcon\Db 支持。Phalcon\Logger 与 Phalcon\Db 交互工作，可以提供数据库抽象层的日志记录功能，从而使我们能够记录下SQL语句。


$di->set('db', function() {

    $eventsManager = new Phalcon\Events\Manager();

    $logger = new Phalcon\Logger\Adapter\File("app/logs/debug.log");

    //Listen all the database events
    $eventsManager->attach('db', function($event, $connection) use ($logger) {
        if ($event->getType() == 'beforeQuery') {
            $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
        }
        if ($event->getType() == 'beforeSave') {
            $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
        }
    });

        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host" => "localhost",
            "username" => "root",
            "password" => "secret",
            "dbname" => "invo",
            "charset"=> "utf8"
        ));

        //Assign the eventsManager to the db adapter instance
        $connection->setEventsManager($eventsManager);

        return $connection;
});


剖析SQL语句
    
感谢 Phalcon\Db ，作为 Phalcon\Mvc\Model 的基本组成部分，剖析ORM产生的SQL语句变得可能，以便分析数据库的性能问题，同时你可以诊断性能问题，并发现瓶颈。
    
$di->set('profiler', function(){
    return new Phalcon\Db\Profiler();
});

$di->set('db', function() use ($di) {

    $eventsManager = new Phalcon\Events\Manager();

    //Get a shared instance of the DbProfiler
    $profiler = $di->getProfiler();

    //Listen all the database events
    $eventsManager->attach('db', function($event, $connection) use ($profiler) {
        if ($event->getType() == 'beforeQuery') {
            $profiler->startProfile($connection->getSQLStatement());
        }
        if ($event->getType() == 'afterQuery') {
            $profiler->stopProfile();
        }
    });

        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host" => "localhost",
            "username" => "root",
            "password" => "secret",
            "dbname" => "invo"
        ));

        //Assign the eventsManager to the db adapter instance
        $connection->setEventsManager($eventsManager);

        return $connection;
});


Profiling some queries:

<?php

// Send some SQL statements to the database
Robots::find();
Robots::find(array("order" => "name");
Robots::find(array("limit" => 30);

//Get the generated profiles from the profiler
$profiles = $di->getShared('profiler')->getProfiles();

foreach ($profiles as $profile) {
    echo "SQL Statement: ", $profile->getSQLStatement(), "\n";
    echo "Start Time: ", $profile->getInitialTime(), "\n";
    echo "Final Time: ", $profile->getFinalTime(), "\n";
    echo "Total Elapsed Time: ", $profile->getTotalElapsedSeconds(), "\n";
}

    每个生成的profile文件，都是以毫秒为单位。