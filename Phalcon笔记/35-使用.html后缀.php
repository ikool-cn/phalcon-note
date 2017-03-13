<?php
$di->set('dispatcher', function() {
    
	//Create an EventsManager
	$eventsManager = new \Phalcon\Events\Manager();

	//Remove extension before dispatch
	$eventsManager->attach("dispatch:beforeDispatchLoop", function($event, $dispatcher) {

		//Remove extension
		$action = preg_replace('/\.html$/', '', $dispatcher->getActionName());

		//Override action
		$dispatcher->setActionName($action);
	});

	$dispatcher = new \Phalcon\Mvc\Dispatcher();
	$dispatcher->setEventsManager($eventsManager);

	return $dispatcher;
});