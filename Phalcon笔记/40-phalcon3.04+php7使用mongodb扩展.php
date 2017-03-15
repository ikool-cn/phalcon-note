框架本身支持的是mongo扩展。此扩展已经放弃维护了支持php5.x
而php7只能用mongodb扩展。框架中要使用的话需要composer下载phalcon出的额外的孵化器项目https://github.com/phalcon/incubator

Loader
<?php
$loader->registerDirs(
    [
        "../app/controllers/",
        "../app/models/",
        "../app/collections/", //mongodb model
    ]
);
$loader->registerNamespaces([
    'Phalcon' => '../vendor/phalcon/incubator/Library/Phalcon/'
]);
DI

$di->set('config', function () {
    return require APP_PATH . '/config/config.php';
});
 
// Initialise the mongo DB connection.
$di->setShared('mongo', function () {
    /** @var \Phalcon\DiInterface $this */
    $config = $this->getShared('config');
 
    if (!$config->database->mongo->username || !$config->database->mongo->password) {
        $dsn = 'mongodb://' . $config->database->mongo->host;
    } else {
        $dsn = sprintf(
            'mongodb://%s:%s@%s:%s',
            $config->database->mongo->username,
            $config->database->mongo->password,
            $config->database->mongo->host,
            $config->database->mongo->port
        );
    }
 
    $mongo = new Client($dsn);
 
    return $mongo->selectDatabase($config->database->mongo->dbname);
});
 
// Collection Manager is required for MongoDB
$di->setShared('collectionManager', function () {
    return new Manager();
});
我们在app/collections下面新建一个UserCollection.php的模型

<?php
use Phalcon\Mvc\MongoCollection;
 
class UserCollection extends MongoCollection
{
    public $name;
    public $email;
    public $password;
    public $skill;
 
    public function getSource()
    {
        return 'user';
    }
}
控制器中使用：

<?php
use Phalcon\Mvc\Controller;
 
class IndexController extends Controller
{
 
    function indexAction()
    {
        //创建
        $userCollection = new UserCollection();
        $userCollection->email = '123456@qq.com';
        $userCollection->name = 'allen';
        $userCollection->password = md5('123456');
        $userCollection->skill = [
            'habit' => [
                'program' => [
                    ['name' => 'php', 'year' => 8],
                    ['name' => 'golang', 'year' => 5]
                ],
                'sport' => [
                    ['name' => 'football', 'year' => 6],
                    ['name' => 'basketball', 'year' => 3]
                ],
            ]
        ];
        var_dump($userCollection->save());
        echo  $userCollection->getId();
 
        //更新
        $res = UserCollection::findById(new \MongoDB\BSON\ObjectID('58c7f0867e2b1e10e8006242'));
        $res->name = 'ikool2';
        $res->save();
 
        //删除
        $res = UserCollection::findFirst();
        if ($res !== false) {
            if ($res->delete() === false) {
                echo "Sorry, we can't delete the robot right now: \n";
                $messages = $res->getMessages();
                foreach ($messages as $message) {
                    echo $message, "\n";
                }
            } else {
                echo "The robot was deleted successfully!";
            }
        }
 
        //ID查找
        $res = UserCollection::findById('58c7f0867e2b1e10e8006242');
        print_r($res->toArray());
 
        //count
        echo UserCollection::count([
            [
                'name' => 'ikool'
            ]
        ]);
 
        //查找列表
        $res = UserCollection::find([
            //conditions key 可以不写
            'conditions' => [
                'name' => 'ikool',
                'skill.habit.program' => ['$elemMatch' => ['name' => 'php', 'year' => ['$gte' => 5]]],
            ]
            ,
            'fields' => ['name' => true], //只能全部设置true或者false
            'sort' => ['_id' => -1],//1代表升序，－1代表降
            'skip' => 0,
            'limit'=> 2,
        ]);
 
        foreach ($res as $re) {
            print_r($re->toArray());
        }
 
        //正则
        $regex = new MongoDB\BSON\Regex('^ik');
        $res = UserCollection::find([
            ['name' => $regex]
        ]);
 
        foreach ($res as $re) {
            print_r($re->toArray());
        }
    }
}