<?php
//只能在同一个connection里面切换DB

//首先入口文件

$di->set('db', function ()
{
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '123456',
        'dbname' => 'test_db', //默认是连接到test_db
        'charset' => 'utf8'
    ));
});

//模型
class Users extends Phalcon\Mvc\Model{

    /**
     * @abstract 动态改变数据库映射
     * @see \Phalcon\Mvc\Model::setSchema()
     */
    public function setDb($dbname)
    {
        parent::setSchema($dbname);//protected in \Phalcon\Mvc\Model
    }
}

//控制器
//切换到 数据库 test999 ，注意并没有切换connetction
$users = new Users();
$users->setDb('test999');
$user = $users->findFirst(array('userid = 1'))->toArray();
print_r($user);exit;








//++++++++++ 以上是手动切库 +++++++++++++
//还可以针对某个模型自动切库，也就是指定使用某个数据库
<?php
class Users extends Phalcon\Mvc\Model{
    
	//自动调用的
    public function getSchema()
    {
        return "otherDB";
    }
}

//控制器
$users = new Users();
$user = $users->findFirst(array('userid = 1'))->toArray();
print_r($user);exit;