<?php
http://docs.phalconphp.com/zh/latest/reference/models.html#setting-multiple-databases

//This service returns a MySQL database
$di->set('dbMysql', function() {
     return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => "localhost",
        "username" => "root",
        "password" => "secret",
        "dbname" => "invo"
    ));
});

//This service returns a PostgreSQL database
$di->set('dbPostgres', function() {
     return new \Phalcon\Db\Adapter\Pdo\PostgreSQL(array(
        "host" => "localhost",
        "username" => "postgres",
        "password" => "",
        "dbname" => "invo"
    ));
});


Then, in the Initialize method, we define the connection service for the model:

<?php

class Robots extends \Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->setConnectionService('dbPostgres');//指定连接到：mysql/postgressql/其他
    }

}

But Phalcon offers you more flexibility, you can define the connection that must be used to ‘read’ and for ‘write’. This is specially useful to balance the load to your databases implementing a master-slave architecture:

<?php

class Robots extends \Phalcon\Mvc\Model
{

    public function initialize()
    {
		//主从
        $this->setReadConnectionService('dbSlave');
        $this->setWriteConnectionService('dbMaster');
    }

}


//+++++++++++++++++++ 水平切分数据库 ++++++++++++++++++++++
The ORM also provides Horizontal Sharding facilities, by allowing you to implement a ‘shard’ selection according to the current query conditions:

<?php

class Robots extends Phalcon\Mvc\Model
{
    /**
     * Dynamically selects a shard
     *
     * @param array $intermediate
     * @param array $bindParams
     * @param array $bindTypes
     */
    public function selectReadConnection($intermediate, $bindParams, $bindTypes)
    {
        //Check if there is a 'where' clause in the select
        if (isset($intermediate['where'])) {

            $conditions = $intermediate['where'];

            //Choose the possible shard according to the conditions
			//水平切分数据库
            if ($conditions['left']['name'] == 'id') {
                $id = $conditions['right']['value'];
                if ($id > 0 && $id < 10000) {
                    return $this->getDI()->get('dbShard1');
                }
                if ($id > 10000) {
                    return $this->getDI()->get('dbShard2');
                }
            }
        }

        //Use a default shard
        return $this->getDI()->get('dbShard0');
    }

}

The method ‘selectReadConnection’ is called to choose the right connection, this method intercepts any new query executed:

<?php

$robot = Robots::findFirst('id = 101');










//+++++++++++++++++++ 集群随机选取数据库 ++++++++++++++++++++++

//入口文件
$di->set('dbSlave1', function ()
    {
        return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname' => 'test_db',
            'charset' => 'utf8'
        ));
    });
    
    $di->set('dbSlave2', function ()
    {
        return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname' => 'test',
            'charset' => 'utf8'
        ));
    });
	
	
//模型文件

<?php
class Users extends BaseModel{

    public function initialize()
    {
        $db_list = array('dbSlave1', 'dbSlave2');
        $this->setReadConnectionService($db_list[array_rand($db_list)]);//做一个随机就OK了
        $this->setWriteConnectionService('dbMaster');
    }
}
