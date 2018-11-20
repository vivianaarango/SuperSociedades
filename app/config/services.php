<?php

/**
 * Services are globally registered in this file
 */
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Db\Adapter\Pdo\Postgresql as DbAdapter;
use Phalcon\Mvc\View;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Collection\Manager as CollectionManager;
use Phalcon\Crypt;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Config metadata
 */
$config = require __DIR__ . '/config.php';

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
     $config = array(
         "schema" => $config->database->schema,
         "host" => $config->database->host,
         "dbname" => $config->database->dbname,
         "username" => $config->database->username,
         "password" => $config->database->password
     );
     return new DbAdapter($config);
});

$di->set('db-cms', function () use ($config) {

    $config = array(
        "schema" => $config->database_cms->schema,
        "host" => $config->database_cms->host,
        "dbname" => $config->database_cms->dbname,
        "username" => $config->database_cms->username,
        "password" => $config->database_cms->password,
    );

    return new DbAdapter($config);

});

/**
 *
 */
$di->set('modelsManager', function() {

    $eventsManager = new EventsManager();
    $modelsManager = new ModelsManager();
    $modelsManager->setEventsManager($eventsManager);

    return $modelsManager;
});


/**
 * Registering the collectionManager service
 */
$di->set('collectionManager', function() {

    $eventsManager = new EventsManager();
    $modelsManager = new CollectionManager();
    return $modelsManager;

}, true);

/**
 * Phalcon Crypt based on mcrypt library
 */
$di->set('crypt', function() {
    $crypt = new Crypt();
    return $crypt;
}, true);

/**
 *
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
}, true);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () {

    $url = new UrlResolver();
    $url->setBaseUri('/api_devel/');

    return $url;
});

/**
 * Disable render views
 */
$di->set('view', function() {

    $view = new View();

    //Disable several levels
    $view->disableLevel(array(
        View::LEVEL_NO_RENDER => true,
    ));

    return $view;

}, true);

/**
 *
 */
$di->set("response", function (){
    return new \Phalcon\Http\Response();
});

$di->setShared('dispatcher', function() {

    //Create/Get an EventManager
    $eventsManager = new Phalcon\Events\Manager();

    //Attach a listener
    $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {

        //The controller exists but the action not
        if ($event->getType() == 'beforeNotFoundAction') {
            $dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'show404'
            ));
            return false;
        }

        //Alternative way, controller or action doesn't exist
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'controller' => 'index',
                        'action' => 'show404'
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

/**
 *
 */
$di->set("request", function () {
    return new HttpRequestCurl();
});

/**
 *
 */
$di->set("mandrill", function () {
    return new Mandrill($config->mandrill->key);
});

/**
 *
 */
$di->set("co2", function () {
    return new Co2();
});

/**
 *
 */
$di->set("pusher", function () {
    return new Pusher("7c98243433ca78bdd1ca", "ce2d74f42f5379e10ee8", "146862");
});

/**
 *
 */
$di->set('application', function() use ($config){
    return $config->application;
});

/**
 * Become all warning and notice in throw exception
 */
set_error_handler(function($num, $str, $file, $line, $context = null)
{
    throw new \ErrorException($str, 0, $num, $file, $line);
});
