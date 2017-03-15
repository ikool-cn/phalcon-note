��ܱ���֧�ֵ���mongo��չ������չ�Ѿ�����ά����֧��php5.x
��php7ֻ����mongodb��չ�������Ҫʹ�õĻ���Ҫcomposer����phalcon���Ķ���ķ�������Ŀhttps://github.com/phalcon/incubator

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
������app/collections�����½�һ��UserCollection.php��ģ��

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
��������ʹ�ã�

<?php
use Phalcon\Mvc\Controller;
 
class IndexController extends Controller
{
 
    function indexAction()
    {
        //����
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
 
        //����
        $res = UserCollection::findById(new \MongoDB\BSON\ObjectID('58c7f0867e2b1e10e8006242'));
        $res->name = 'ikool2';
        $res->save();
 
        //ɾ��
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
 
        //ID����
        $res = UserCollection::findById('58c7f0867e2b1e10e8006242');
        print_r($res->toArray());
 
        //count
        echo UserCollection::count([
            [
                'name' => 'ikool'
            ]
        ]);
 
        //�����б�
        $res = UserCollection::find([
            //conditions key ���Բ�д
            'conditions' => [
                'name' => 'ikool',
                'skill.habit.program' => ['$elemMatch' => ['name' => 'php', 'year' => ['$gte' => 5]]],
            ]
            ,
            'fields' => ['name' => true], //ֻ��ȫ������true����false
            'sort' => ['_id' => -1],//1�������򣬣�1����
            'skip' => 0,
            'limit'=> 2,
        ]);
 
        foreach ($res as $re) {
            print_r($re->toArray());
        }
 
        //����
        $regex = new MongoDB\BSON\Regex('^ik');
        $res = UserCollection::find([
            ['name' => $regex]
        ]);
 
        foreach ($res as $re) {
            print_r($re->toArray());
        }
    }
}