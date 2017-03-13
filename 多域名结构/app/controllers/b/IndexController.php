<?php
namespace App\Controllers\B;

use Phalcon\Mvc\Controller;

class IndexController extends Controller{

    function indexAction() {
        $this->view->setVar('module', 'b');
    }
}