<?php
namespace App\Controllers\Home;

use Phalcon\Mvc\Controller;

class IndexController extends Controller{

    function indexAction() {
        $this->view->setVar('module', 'www');
    }
}