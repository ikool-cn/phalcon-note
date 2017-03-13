<?php
$di->set('dispatcher', function(){

    //Create an event manager
    $eventsManager = new \Phalcon\Events\Manager();

    //Attach a listener for type "dispatch"
    $eventsManager->attach("dispatch", function($event, $dispatcher) {
        echo $event->getType(), '<br>';
        ob_flush();
    });

        $dispatcher = new \Phalcon\Mvc\Dispatcher();

        //Bind the eventsManager to the view component
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;

}, true);

//结果
// beforeDispatchLoop
// beforeDispatch
// beforeExecuteRoute
// afterInitialize

//循环调度事件还有些是在控制器
//详情见：http://docs.phalconphp.com/zh/latest/reference/dispatching.html#dispatch-loop-events