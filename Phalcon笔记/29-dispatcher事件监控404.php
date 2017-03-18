<?php


$di->setShared('dispatcher', function() {

    //Create/Get an EventManager
    $eventsManager = new Phalcon\Events\Manager();

    //Attach a listener
    $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {

        //The controller exists but the action not
        if ($event->getType() == 'beforeNotFoundAction') {
            $dispatcher->forward(array(
                'namespace' => 'App\Controllers\Home',
                'controller' => 'err',
                'action' => 'show404',
                'params' => ''
            ));
            return false;
        }

        //Alternative way, controller or action doesn't exist
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'namespace' => 'App\Controllers\Home',
                        'controller' => 'err',
                        'action' => 'show404',
                        'params' => ''
                    ));
                    return false;
            }
        }
    });

    $dispatcher = new Phalcon\Mvc\Dispatcher();

    //Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

//+++++++++++++++++++++++++++++++++  或者 +++++++++++++++++++++++++++++++++++++++++

$di->set('dispatcher', function() {	
	//Create an EventsManager
	$eventsManager = new Phalcon\Events\Manager();

	//Attach a listener
	$eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {

		//Handle 404 exceptions
		if ($exception instanceof Phalcon\Mvc\Dispatcher\Exception) {
			$dispatcher->forward(array(
				'controller' => 'Public',
				'action' => 'notfound'
			));
			return false;
		}
	});

	$dispatcher = new \Phalcon\Mvc\Dispatcher();

	//Bind the EventsManager to the dispatcher
	$dispatcher->setEventsManager($eventsManager);

	return $dispatcher;

}, true);

//+++++++++++++++++++++++++++++++++ 或者使用类 ++++++++++++++++++++++++++
<?php

use Phalcon\Mvc\Dispatcher,
    Phalcon\Events\Event,
    Phalcon\Mvc\Dispatcher\Exception as DispatchException;

class ExceptionsPlugin
{
    public function beforeException(Event $event, Dispatcher $dispatcher, $exception)
    {

        //Handle 404 exceptions
        if ($exception instanceof DispatchException) {
            $dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'show404'
            ));
            return false;
        }

        //Handle other exceptions
        $dispatcher->forward(array(
            'controller' => 'index',
            'action' => 'show503'
        ));

        return false;
    }
}

//入口
$di->set('dispatcher', function() {	
	//Create an EventsManager
	$eventsManager = new Phalcon\Events\Manager();

	//Attach a listener
	$eventsManager->attach("dispatch:beforeException", new ExceptionsPlugin());

	$dispatcher = new \Phalcon\Mvc\Dispatcher();

	//Bind the EventsManager to the dispatcher
	$dispatcher->setEventsManager($eventsManager);

	return $dispatcher;

}, true);