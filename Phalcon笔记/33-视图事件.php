<?php


$di->set('view', function() {

    //Create an events manager
    $eventsManager = new Phalcon\Events\Manager();

    //Attach a listener for type "view"
    $eventsManager->attach("view", function($event, $view) {
        echo $event->getType(), ' - ', $view->getActiveRenderPath(), PHP_EOL;
        ob_flush();
    });

        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir("../app/views/");

        //Bind the eventsManager to the view component
        $view->setEventsManager($eventsManager);

        return $view;

}, true);


//可以观察一下 视图渲染的过程中的事件

/*     beforeRender -
    beforeRenderView - ../app/views/index/index.phtml
    <h1>这是视图代码</h1>afterRenderView - ../app/views/index/index.phtml
    notFoundView - ../app/views/layouts/index.phtml
    notFoundView - ../app/views/index.phtml
    afterRender - ../app/views/index.phtml
    <h1>这是视图代码</h1> */
	
	
下面的示例演示如何创建一个插件 Tidy ，清理/修复的渲染过程中产生的HTML：

<?php

class TidyPlugin
{

    public function afterRender($event, $view)
    {

        $tidyConfig = array(
            'clean' => true,
            'output-xhtml' => true,
            'show-body-only' => true,
            'wrap' => 0,
        );

        $tidy = tidy_parse_string($view->getContent(), $tidyConfig, 'UTF8');
        $tidy->cleanRepair();

        $view->setContent((string) $tidy);
    }

}

//Attach the plugin as a listener
$eventsManager->attach("view:afterRender", new TidyPlugin());