<?php
$phql = 'SELECT id,username FROM Users WHERE username like :username:';
//第1种
$users = $this->modelsManager->executeQuery($phql, array('username' => '%demo%'));

//第2种
$users = $this->modelsManager->createQuery($phql)->execute(array('username' => '%demo%'));

//第3种
$users = $this->modelsManager->createBuilder()->from('Users')->columns(array('id', 'username'))->where('username like :username:')->getQuery()->execute(array('username' => '%demo%'));

//第4种
$query = new Phalcon\Mvc\Model\Query($phql, $this->getDI());
$users = $query->execute(array('username like' => '%demo%'));

//第5种
$criteria = new Phalcon\Mvc\Model\Criteria();
$users = $criteria->setModelName('Users')->columns(array('id', 'username'))->where('username like :username:')->bind(array('username' => '%demo%'))->execute();

//第6种
$users = Users::find(array('columns' => 'id,username', 'conditions' => 'username like :username:', 'bind' => array('username' => '%demo%')));

//第7种
$users = Users::query()->where('username like :username:')->columns(array('id', 'username'))->bind(array('username' => '%demo%'))->execute();

//第8种 数据库抽象层（Database Abstraction Layer）
//http://docs.phalconphp.com/zh/latest/reference/db.html

$sql = "SELECT id,username FROM Users WHERE username like '%demo%'";
$query = $this->db->query($sql);
// Get all rows in an array
$robots = $this->db->fetchAll($sql);
foreach ($robots as $robot) {
   print_r($robot);exit;
}

// 发送SQL语句到数据库
$result = $connection->query($sql);

// 打印每个robot名称
while ($robot = $result->fetch()) {
   echo $robot["name"];
}

// 返回一个包含返回结果的数组
$robots = $connection->fetchAll($sql);
foreach ($robots as $robot) {
   echo $robot["name"];
}
详情看相关文档。