<?php
class IndexController extends Phalcon\Mvc\Controller{
		
	public function indexAction(){
		
		$view = $this->getDI()->get('view');
		$view->start();
		$view->render('index', 'index');
		$view->finish();
		$output = $view->getContent();
		file_put_contents('./index_index.html', $output);
		$view->disable();
		echo 'ok';exit;
	}
}