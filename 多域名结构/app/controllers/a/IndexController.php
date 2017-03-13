<?php
namespace App\Controllers\A;

use Phalcon\Mvc\Controller;

class IndexController extends Controller{

    function indexAction() {
        $this->view->setVar('module', 'a');
    }
}