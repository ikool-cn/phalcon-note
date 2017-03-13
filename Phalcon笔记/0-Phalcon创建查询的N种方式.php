<?php
$phql = 'SELECT id,username FROM Users WHERE username like :username:';
//��1��
$users = $this->modelsManager->executeQuery($phql, array('username' => '%demo%'));

//��2��
$users = $this->modelsManager->createQuery($phql)->execute(array('username' => '%demo%'));

//��3��
$users = $this->modelsManager->createBuilder()->from('Users')->columns(array('id', 'username'))->where('username like :username:')->getQuery()->execute(array('username' => '%demo%'));

//��4��
$query = new Phalcon\Mvc\Model\Query($phql, $this->getDI());
$users = $query->execute(array('username like' => '%demo%'));

//��5��
$criteria = new Phalcon\Mvc\Model\Criteria();
$users = $criteria->setModelName('Users')->columns(array('id', 'username'))->where('username like :username:')->bind(array('username' => '%demo%'))->execute();

//��6��
$users = Users::find(array('columns' => 'id,username', 'conditions' => 'username like :username:', 'bind' => array('username' => '%demo%')));

//��7��
$users = Users::query()->where('username like :username:')->columns(array('id', 'username'))->bind(array('username' => '%demo%'))->execute();

//��8�� ���ݿ����㣨Database Abstraction Layer��
//http://docs.phalconphp.com/zh/latest/reference/db.html

$sql = "SELECT id,username FROM Users WHERE username like '%demo%'";
$query = $this->db->query($sql);
// Get all rows in an array
$robots = $this->db->fetchAll($sql);
foreach ($robots as $robot) {
   print_r($robot);exit;
}

// ����SQL��䵽���ݿ�
$result = $connection->query($sql);

// ��ӡÿ��robot����
while ($robot = $result->fetch()) {
   echo $robot["name"];
}

// ����һ���������ؽ��������
$robots = $connection->fetchAll($sql);
foreach ($robots as $robot) {
   echo $robot["name"];
}
���鿴����ĵ���