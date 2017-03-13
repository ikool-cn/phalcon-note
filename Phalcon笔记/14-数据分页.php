<?php
// Phalcon给我们提供了三种数据适配器来处理分页。

// 1、NativeArray 	Use a PHP array as source data
// 2、Model 	Use a Phalcon\Mvc\Model\Resultset object as source data. Since PDO doesn’t support scrollable cursors this adapter shouldn’t be used to paginate a large number of records
// 3、QueryBuilder 	Use a Phalcon\Mvc\Model\Query\Builder object as source data

// 其中前两种需要查询出所有的数据然后分页，第三种支持大数据。


// 示例（Examples）

// In the example below, the paginator will use as its source data the result of a query from a model, and limit the displayed data to 10 records per page:



// Current page to show
// In a controller this can be:
// $this->request->getQuery('page', 'int', 1); // GET
// $this->request->getPost('page', 'int', 1); // POST
$currentPage = (int) $_GET["page"];

// The data set to paginate
$robots = Robots::find();

// Create a Model paginator, show 10 rows by page starting from $currentPage
$paginator = new \Phalcon\Paginator\Adapter\Model(
    array(
        "data" => $robots,
        "limit"=> 10,
        "page" => $currentPage
    )
);

// Get the paginated results
$page = $paginator->getPaginate();

//Variable $currentPage controls the page to be displayed. The $paginator->getPaginate() returns a $page object that contains the paginated data. It can be used for generating the pagination:

<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Type</th>
    </tr>
    <?php foreach ($page->items as $item) { ?>
    <tr>
        <td><?php echo $item->id; ?></td>
        <td><?php echo $item->name; ?></td>
        <td><?php echo $item->type; ?></td>
    </tr>
    <?php } ?>
</table>

The $page object also contains navigation data:

<a href="/robots/search">First</a>
<a href="/robots/search?page=<?= $page->before; ?>">Previous</a>
<a href="/robots/search?page=<?= $page->next; ?>">Next</a>
<a href="/robots/search?page=<?= $page->last; ?>">Last</a>

<?php echo "You are in page ", $page->current, " of ", $page->total_pages; ?>

适配器使用（Adapters Usage）

An example of the source data that must be used for each adapter:

<?php

//Passing a resultset as data
$paginator = new \Phalcon\Paginator\Adapter\Model(
    array(
        "data"  => Products::find(),
        "limit" => 10,
        "page"  => $currentPage
    )
);

//Passing an array as data
$paginator = new \Phalcon\Paginator\Adapter\NativeArray(
    array(
        "data"  => array(
            array('id' => 1, 'name' => 'Artichoke'),
            array('id' => 2, 'name' => 'Carrots'),
            array('id' => 3, 'name' => 'Beet'),
            array('id' => 4, 'name' => 'Lettuce'),
            array('id' => 5, 'name' => '')
        ),
        "limit" => 2,
        "page"  => $currentPage
    )
);

//Passing a querybuilder as data

$builder = $this->modelsManager->createBuilder()
    ->columns('id, name')
    ->from('Robots')
    ->orderBy('name');

$paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
    "builder" => $builder,
    "limit"=> 20,
    "page" => 1
));

页面属性（Page Attributes）¶

The $page object has the following attributes:
Attribute 	Description
items 	The set of records to be displayed at the current page
current 	The current page
before 	The previous page to the current one
next 	The next page to the current one
last 	The last page in the set of records
total_pages 	The number of pages
total_items 	The number of items in the source data
自定义适配器（Implementing your own adapters）¶

The Phalcon\Paginator\AdapterInterface interface must be implemented in order to create your own paginator adapters or extend the existing ones:

<?php

class MyPaginator implements Phalcon\Paginator\AdapterInterface
{

    /**
     * Adapter constructor
     *
     * @param array $config
     */
    public function __construct($config);

    /**
     * Set the current page number
     *
     * @param int $page
     */
    public function setCurrentPage($page);

    /**
     * Returns a slice of the resultset to show in the pagination
     *
     * @return stdClass
     */
    public function getPaginate();

}

//++++++++++++++++++++++ 正式环境测试表明 QueryBuilder 也无法胜任大数据分页，以下是DEBUG SQL语句 ++++++++++++++++
2 Connect	root@localhost on test_db
2 Query	SELECT IF(COUNT(*)>0, 1 , 0) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME`='users'
2 Query	DESCRIBE `users`
2 Query	SELECT `users`.`id`, `users`.`username`, `users`.`password`, `users`.`name`, `users`.`email`, `users`.`created_at`, `users`.`active` FROM `users` LIMIT 10 OFFSET 42550
2 Query	SELECT COUNT(*) "rowcount" FROM (SELECT users.* FROM `users`) AS T
2 Quit	

//问题出在统计总数的 COUNT(*)这里，这个sql写的有问题，不知道为啥phalcon会写出这么傻逼的sql语句来





/////////// 推荐使用自定义分页 /////////////////////
//获取页码
$currentPage  = $this->request->getQuery('page', 'int', 1);

//获取总条数、总页数、以及$offset
$limit = 10;
$phql = 'SELECT COUNT(*) AS total_items FROM Users WHERE active = :active:';
$total_items = $this->modelsManager->executeQuery($phql, array('active' => 'Y'))->getFirst()->total_items;
$total_pages = ceil($total_items / $limit);
$currentPage  = $currentPage > $total_pages ? $total_pages : $currentPage;
$offset = ($currentPage - 1) * $limit;

//查询列表数据
$phql = "SELECT id,username,email FROM Users WHERE active = :active: LIMIT {$offset}, {$limit}";
$users = $this->modelsManager->executeQuery($phql, array('active' => 'Y'));
var_dump($users);exit;


