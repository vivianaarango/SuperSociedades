<?php

use Phalcon\Mvc\Application;

date_default_timezone_set('America/Bogota');

error_reporting(E_ALL);

define('API_KEY_SERVICE', md5('L0G15T1C44P_LR35$*ZF45T163516r35'));
define('API_REST_TOKEN', "RESTLOGISTICAPP0");

echo "";

try {

	/**
	 * Config database data
	 */
	$config = require __DIR__ . '/../app/config/config.php';

	/**
	 * Load directories to app
	 */
	require __DIR__ . '/../app/config/loader.php';

	/**
	 * Load dependecy injector
	 */
	require __DIR__ . '/../app/config/services.php';

	/**
	 * Handle the request
	 */
	$application = new Application();

	/**
	 * Assign the DI
	 */
	$application->setDI($di);

	/**
	 * Print all services
	 */
	echo $application->handle()->getContent();

} catch (\Exception $e) {

    echo json_encode(array(
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "Line" => $e->getLine(),
        "trace" => $e->getTraceAsString()
    ));
}
