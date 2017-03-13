<?php
//观察一下这几个方法的执行顺序
class IndexController extends \Phalcon\mvc\Controller{
    
    function initialize(){
        echo 'initialize' . '<br>';
    }
    
    function onConstruct(){
        echo 'onConstruct'. '<br>';
    }
    
    function beforeExecuteRoute($dispatcher)
    {
		echo 'beforeExecuteRoute';
        // This is executed before every found action
        if ($dispatcher->getActionName() == 'save') {

            $this->flash->error("You don't have permission to save posts");

            $this->dispatcher->forward(array(
                'controller' => 'home',
                'action' => 'index'
            ));

            return false;
        }
    }
    
    function afterExecuteRoute(){
        echo 'afterExecuteRoute'. '<br>';
    }
    
    function indexAction(){
        echo 'indexAction'. '<br>';
    }
}

// onConstruct
// beforeExecuteRoute
// initialize
// indexAction
// afterExecuteRoute


beforeExecuteRoute 和 initialize 区别：
前者可以通过return false 来中断控制器方法的执行。后者不可以。

class IndexController extends Phalcon\Mvc\Controller{
    
    public function beforeExecuteRoute()
    {
		//一些逻辑处理
		//...
		//可以做错误提示
        $this->view->pick('error/show');
        return false;//可以中断控制器的action的执行，直接返回
    }
    
    public function initialize()
    {
		//不可以
        $this->view->pick('error/show');
        return false;//还会继续执行控制器的action
    }
	
	
//如果想要在initialize方法里渲染一个视图然后，终止执行action也是可以的。参考http://docs.phalconphp.com/zh/latest/api/Phalcon_Mvc_View.html

    public function initialize()
    {
        //Setting views directory
         $view = new Phalcon\Mvc\View();
         $view->setViewsDir('../app/views/');
        
         $view->start();
         $view->render('index', 'test');
         $view->finish();
        
         //Printing views output
         echo $view->getContent();exit;//必须要退出，否则会倍action覆盖。

    }

    public function indexAction(){

    }
